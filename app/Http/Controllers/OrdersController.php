<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Address;
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
        //创建订单
        $order = $this->order_service->store( $user, $address, $request->remark, $request->items );

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


}
