<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductRating {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( OrderReviewed $event ) {
        //
        $items = $event->getOrder()->items()->with( 'product' )->get();
        foreach ( $items as $item ) {
            //获取商品对应的评价数量和评价分
            $result = OrderItem::query()
                               ->where( 'product_id', $item->product_id )//当前订单项对应的商品
                               ->whereNotNull( 'reviewed_at' )//已评价的
                               ->whereHas( 'order', function ( $query ) {
                    $query->whereNotNull( 'paid_at' );//对应的订单已支付的
                } )
                //使用first 可以传入查询字段数组，使用原生查询可以原封不动的把sql条件传进去
                               ->first( [
                    \DB::raw( 'count(*) as review_count' ),//商品评价条数
                    \DB::raw( 'avg(rating) as rating' ),//平均局
                ] );
            //更新商品
            $item->product->update( [
                'review_count' => $result->review_count,
                'rating'       => $result->rating
            ] );
        }
    }
}
