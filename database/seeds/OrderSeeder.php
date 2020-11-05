<?php

use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //获取faker实例

        $faker = app( Faker\Generator::class );
        //创建100条订单
        $orders = factory( \App\Models\Order::class, 100 )->create();

        //保存创建订单的所有商品，用于后面的商品评分
        $products = collect( [] );
        //创建对应的订单项
        foreach ( $orders as $order ) {
            //创建订单项
            $items = factory( \App\Models\OrderItem::class )->times( random_int( 1, 5 ) )->create( [
                'order_id'    => $order->id,
                'rating'      => $order->reviewed ? random_int( 1, 5 ) : null,
                // 随机评分 1 - 5
                'review'      => $order->reviewed ? $faker->sentence : null,
                'reviewed_at' => $order->reviewed ? $faker->dateTimeBetween( $order->paid_at ) : null,
                // 评价时间不能早于支付时间
            ] );
            //计算订单项总金额
            $total_amount = 0;
            foreach ( $items as $item ) {
                $total_amount += $item->price;
            }
            //优惠后价格
            if ( $order->coupon_code_id ) {
                $total_amount = $order->coupon->countTotalAmount( $total_amount );
            }
            //更新订单总价
            $order->update( [ 'total_amount' => $total_amount ] );

            //取出所有商品合并到products
            //items  product关联的商品
            $products = $products->merge( $items->pluck( 'product' ) );
        }

        //商品评分
        //过滤重复的商品项 id相同,计算每一项的销量评分
        $products->unique( 'id' )->each( function ( \App\Models\Product $product ) {
            // 查出该商品的销量、评分、评价数
            //$sql = "select * from order_items where product_id = $product->id ";
            $result = \App\Models\OrderItem::query()
                                           ->where( 'product_id', $product->id )
                                           ->whereHas( 'order', function ( $query ) {
                                               $query->whereNotNull( 'paid_at' );
                                           } )
                                           ->first( [
                                               \DB::raw( 'count(*) as review_count' ),
                                               \DB::raw( 'avg(rating) as rating' ),
                                               \DB::raw( 'sum(amount) as sold_count' ),
                                           ] );

            $product->update( [
                'rating'       => $result->rating ?: 5, // 如果某个商品没有评分，则默认为 5 分
                'review_count' => $result->review_count,
                'sold_count'   => $result->sold_count,
            ] );
        } );
    }
}
