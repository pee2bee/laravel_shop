<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Jobs\CloseOrder;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductSku;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller {

    protected $order_service;

    public function __construct( OrderService $order_service ) {
        $this->order_service = $order_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //

        $orders = $this->order_service->get();

        return view( 'users.order', compact( 'orders' ) );
    }


    /**
     * @param OrderRequest $request
     *
     * @return mixed
     * @throws \Throwable
     */
    public function store( OrderRequest $request ) {

        $user    = $request->user();
        $address = Address::find( $request->address_id );

        //如果用户输入了优惠码，先检查优惠券是否可用（未对满减验证，需要订单总额）
        if ( $coupon_code = $request->coupon ) {
            Coupon::checkCodeValid( $coupon_code );
        }

        //创建订单，传参用户，地址，备注，商品项，优惠码
        $order = $this->order_service->store( $user, $address, $request->remark, $request->items, $coupon_code );

        //触发定时关闭订单任务
        $this->dispatch( new CloseOrder( $order, config( 'app.order_ttl' ) ) );

        return $order;

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id ) {
        //

        $order = Order::query()->with( 'items.product', 'items.productSku' )->find( $id );

        //权限判断
        $this->authorize( 'own', $order );

        return view( 'users.order_show', compact( 'order' ) );

    }

    public function received( Order $order, Request $request ) {
        //校验权限
        $this->authorize( 'own', $order );
        //判断订单是否为已发货
        if ( $order->ship_status != Order::SHIP_STATUS_DELIVERED ) {
            throw new InvalidRequestException( '发货状态不正确' );
        }

        //更新发货状态为已收到
        $order->update( [ 'ship_status' => Order::SHIP_STATUS_RECEIVED ] );

        //返回
        return '';
    }

    //展示评价页面
    public function review( Order $order ) {
        //校验权限
        $this->authorize( 'own', $order );
        //判断是否已支付
        if ( ! $order->paid_at ) {
            throw new InvalidRequestException( '订单未支付，不能进行评价' );
        }
        //预加载关联
        $order->load( 'items.product', 'items.productSku' );

        return view( 'users.order_review', compact( 'order' ) );
    }

    //保存评价到orderItem
    public function sendReview( Order $order, SendReviewRequest $request ) {

        //校验权限
        $this->authorize( 'own', $order );
        //判断是否已支付
        if ( ! $order->paid_at ) {
            throw new InvalidRequestException( '订单未支付，不可以评价' );
        }

        //判断是否已进行过评价
        if ( $order->reviewed ) {
            throw new InvalidRequestException( '该订单已评价，不可重复提交' );
        }

        //获取评价数组
        $reviews = $request->reviews;
        //pan
        //开启事务
        \DB::transaction( function () use ( $reviews, $order ) {
            //遍历reviews 数组
            foreach ( $reviews as $key => $review ) {
                //获取关联的orderItem
                $orderItem = $order->items()->find( $review['id'] );

                //保存评分和评价
                $orderItem->update( [
                    'rating' => $review['rating'],
                    'review' => $review['review']
                ] );
            }
            //将订单标记为已评价
            $order->update( [ 'reviewed' => true ] );
        } );

        //发送已评价事件
        event( new OrderReviewed( $order ) );

        return redirect()->back();
    }

    public function applyRefund( Order $order, ApplyRefundRequest $request ) {

        //判断权限
        $this->authorize( 'own', $order );
        //判断是否已经支付，支付状态正不正确
        if ( ! $order->paid_at ) {
            throw new InvalidRequestException( '该订单未支付' );
        }
        //判断该订单退款状态
        if ( $order->refund_status !== Order::REFUND_STATUS_PENDING ) {
            throw new InvalidRequestException( '该订单已申请过退款,请勿重复申请' );
        }
        $extra                  = $order->extra ?: [];
        $extra['refund_reason'] = $request->reason;
        //将订单退款状态改为已申请退款
        $order->refund_status = Order::REFUND_STATUS_APPLIED;
        //更新extra字段
        $order->extra = $extra;
        $order->save();

        return $order;
    }

}
