<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductSoldCount {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * 更新产品每个sku销量数据，更新商品的总销量
     * Handle the event.
     *
     * @param OrderPaid $event
     *
     * @return void
     */
    public function handle( OrderPaid $event ) {
        //取出关联的订单
        $order = $event->getOrder();
        //预加载对应的商品
        $order->load( 'items.product' );
        //遍历商品
        foreach ( $order->items as $item ) {
            $product = $item->product;
            //计算商品对应的销量,把当前订单条的销量加到商品销量中
            $product->increment( 'sold_count', $item->amount );
        }
    }
}
