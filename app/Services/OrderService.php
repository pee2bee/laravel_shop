<?php
/**
 *
 * @author woojuan
 * @email woojuan163@163.com
 * @copyright GPL
 * @version
 */

namespace App\Services;


use App\Exceptions\InvalidRequestException;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\DomCrawler\AbstractUriElement;

class OrderService {

    public function get() {
        $user = \Auth::user();

        return $orders = $user->orders()->with( 'items.product', 'items.productSku' )->paginate( 10 );
    }

    public function store( User $user, Address $address, $remark, $items, $coupon_code = null ) {

        //开启数据库事务
        $order = \DB::transaction( function () use ( $user, $address, $remark, $items, $coupon_code ) {

            //更新地址的最后使用时间
            $address->update( [ 'last_used_at' => Carbon::now() ] );
            //创建订单
            $order = new Order( [
                'address'      => [
                    'address'       => $address->full_address,
                    'zipcode'       => $address->zipcode,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark'       => $remark,
                'total_amount' => 0
            ] );
            //订单关联到用户
            $order->user()->associate( $user );//绑定用户模型到order， associate()是推荐的用法，直接id赋值也可以
            //写入数据库
            $order->save();

            //计算订单总额
            $totalAmount = 0;
            foreach ( $items as $item ) {
                $sku = ProductSku::find( $item['id'] );
                //创建一个orderItem 并与当前订单关联
                //$order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，
                //这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);。
                $orderItem = $order->items()->make( [
                    'amount' => $item['amount'],
                    'price'  => $sku->price,
                ] );
                $orderItem->product()->associate( $sku->product_id );
                $orderItem->productSku()->associate( $sku );
                $orderItem->save();
                $totalAmount += $sku->price * $item['amount'];
                //减库存
                if ( $sku->decreaseStock( $item['amount'] ) <= 0 ) {
                    //如果减库存失败则抛出异常，由于这块代码是在 DB::transaction() 中执行的，
                    //因此抛出异常时会触发事务的回滚，之前创建的 orders 和 order_items 记录都会被撤销。
                    throw new InvalidRequestException( '该商品库存不足' );
                }
            }


            //检查优惠券是否在满减也可用,如果这里不通过则整个事务都会失败，前面创建的订单也就不会再存在
            if ( $coupon_code ) {
                //检查优惠券对该订单是否可用
                //先更新订单总额,不然总额为0
                $order->update( [ 'total_amount' => $totalAmount ] );
                $coupon = Coupon::checkCodeValid( $coupon_code, $order );
                //根据优惠券类型来计算金额
                $totalAmount           = $coupon->countTotalAmount( $totalAmount );
                $order->coupon_code_id = $coupon->id;//更新优惠券id到订单
                //优惠券使用数量自增
                $coupon->used ++;
                $coupon->save();
            }

            //更新订单总额
            $order->update( [ 'total_amount' => $totalAmount ] );

            //将下单的商品从购物车移除
            $skuIds = collect( $items )->pluck( 'id' );
            $user->cartItems()->whereIn( 'product_sku_id', $skuIds )->delete();

            return $order;
        } );

        return $order;
    }
}
