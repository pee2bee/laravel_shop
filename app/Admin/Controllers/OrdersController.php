<?php

namespace App\Admin\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\DisagreeRefundRequest;
use App\Http\Requests\Request;
use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class OrdersController extends AdminController {
    use ValidatesRequests, AuthorizesRequests;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Models\Order';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
        $grid = new Grid( new Order() );

        $grid->model()->orderBy( 'created_at', 'desc' );

        $grid->column( 'id', __( 'Id' ) );
        $grid->column( 'no', __( '订单号' ) );
        $grid->column( 'user.name', __( '买家' ) );

        $grid->column( 'total_amount', __( '总金额' ) );
        $grid->column( 'remark', __( '备注' ) );
        $grid->column( 'paid_at', __( '支付时间' ) )->display( function ( $value ) {
            if ( $value ) {
                $date = new Carbon( $value );

                return $date->format( 'Y-m-d H:i' );
            }

            return '未支付';
        } );
        $grid->column( 'payment_method', __( '支付方式' ) );
        $grid->column( 'payment_no', __( '支付流水号' ) );
        $grid->ship_status( '物流状态' )->display( function ( $value ) {
            return Order::$shipStatusMap[ $value ];
        } );
        $grid->refund_status( '退款状态' )->display( function ( $value ) {
            return Order::$refundStatusMap[ $value ];
        } );
        $grid->column( 'refund_no', __( 'Refund no' ) );
        $grid->closed( '订单状态' )->display( function ( $value ) {
            return $value ? '已关闭' : '';
        } );
        $grid->reviewed( '评价' )->display( function ( $value ) {
            return $value ? '已评价' : '待评价';
        } );

        return $grid;
    }


    public function show( $id, Content $content ) {
        return $content
            ->header( '查看订单' )
            // body 方法可以接受 Laravel 的视图作为参数,这样可以直接使用原有的布局框架，只是改变body
            ->body( view( 'admin.orders.show', [ 'order' => Order::find( $id ) ] ) );
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form() {
        $form = new Form( new Order() );

        $form->text( 'no', __( 'No' ) );
        $form->number( 'user_id', __( 'User id' ) );
        $form->textarea( 'address', __( 'Address' ) );
        $form->decimal( 'total_amount', __( 'Total amount' ) );
        $form->textarea( 'remark', __( 'Remark' ) );
        $form->datetime( 'paid_at', __( 'Paid at' ) )->default( date( 'Y-m-d H:i:s' ) );
        $form->text( 'payment_method', __( 'Payment method' ) );
        $form->text( 'payment_no', __( 'Payment no' ) );
        $form->text( 'refund_status', __( 'Refund status' ) )->default( 'pending' );
        $form->text( 'refund_no', __( 'Refund no' ) );
        $form->switch( 'closed', __( 'Closed' ) );
        $form->switch( 'reviewed', __( 'Reviewed' ) );
        $form->text( 'ship_status', __( 'Ship status' ) )->default( 'pending' );
        $form->textarea( 'ship_data', __( 'Ship data' ) );
        $form->textarea( 'extra', __( 'Extra' ) );

        return $form;
    }

    //输入物流信息
    public function ship( Order $order, Request $request ) {
        //判断当前订单是否未支付
        if ( ! $order->paid_at ) {
            throw new InvalidRequestException( '当前订单未支付' );
        }
        //判断当前订单状态是否已发货
        if ( $order->ship_status !== Order::SHIP_STATUS_PENDING ) {
            throw new InvalidRequestException( '当前订单已发货' );
        }
        //检校输入的值，物流公司和单号
        //validate(输入，rule,message,attribute)
        $data = $this->validate( $request, [
            'express_company' => [ 'required' ],
            'express_no'      => [ 'required' ]
        ], [], [
            'express_company' => '物流公司',
            'express_no'      => '物流单号'
        ] );
        $order->update( [
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            /// 我们在 Order 模型的 $casts 属性里指明了 ship_data 是一个json数组
            // 因此这里可以直接把数组传过去
            'ship_data'   => $data,
        ] );

        //返回上一页
        return redirect()->back();
    }

    public function agreeRefund( Order $order ) {

        //权限验证 留空
        /*$this->authorize('', $order);*/
        if ( $order->refund_status != Order::REFUND_STATUS_APPLIED ) {
            throw new InvalidRequestException( '退款状态不正确' );
        }

        //下面是资金操作
        switch ( $order->payment_method ) {
            case 'wechat':
                //先留空
                break;
            case 'alipay':
                //生成唯一退款单号，退款要求有一个唯一的单号标识
                $refund_no = Order::createRefundNo();
                //调用支付宝实例
                $result = app( 'alipay' )->refund( [
                    'out_trade_no'   => $order->no, //退款对应的订单流水号
                    'refund_amount'  => $order->total_amount,//退款金额
                    'out_request_no' => $refund_no//退款单号
                ] );
                //根据支付宝的文档，返回值中有sub_code就说明退款失败
                if ( $result->sub_code ) {
                    //将退款失败保存到extra
                    $extra                       = $order->extra;
                    $extra['refund_failed_code'] = $result->sub_code;
                    $order->update( [
                        'refund_no'     => $refund_no,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra'         => $extra
                    ] );
                } else {
                    //退款成功，更新退款状态，保存退款单号
                    $order->update( [
                        'refund_no'     => $refund_no,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ] );
                }
                break;
            default:
                //保持健壮性
                throw new InternalException( '未知的支付方式' );
                break;
        }

        return $order;

    }

    public function disagreeRefund( Order $order, DisagreeRefundRequest $request ) {

        //权限判断，留空

        if ( $order->refund_status != Order::REFUND_STATUS_APPLIED ) {
            throw new InvalidRequestException( '退款状态不正确' );
        }

        //修改订单状态为未退款
        $order->refund_status = Order::REFUND_STATUS_PENDING;
        //保存退款信息到extra
        $extra                           = $order->extra ?: [];
        $extra['refund_disagree_reason'] = $request->reason;
        $order->extra                    = $extra;
        $order->save();

        return $order;
    }
}
