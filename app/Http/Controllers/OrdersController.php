<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        //
        $user = $request->user();
        //开启数据库事务
        $order = \DB::transaction(function () use ($user, $request ){
            $address = Address::find($request->address_id);
            //更新地址的最后使用时间
            $address->update(['last_used_at'=>Carbon::now()]);
            //创建订单
            $order = new Order([
                'address' => [
                    'address' => $address->full_address,
                    'zipcode' => $address->zipcode,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $request->remark,
                'total_amount' => 0
            ]);
            //订单关联到用户
            $order->user()->associate($user);//绑定用户模型到order， associate()是推荐的用法，直接id赋值也可以
            //写入数据库
            $order->save();

            //计算订单总额
            $totalAmount = 0;
            $items = $request->items;
            foreach($items as $item){
                $sku = ProductSku::find($item['id']);
                //创建一个orderItem 并与当前订单关联
                //$order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，
                //这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);。
                $orderItem = $order->items()->make([
                    'amount' => $item['amount'],
                    'price' => $sku->price,
                ]);
                $orderItem->product()->associate($sku->product_id);
                $orderItem->productSku()->associate($sku);
                $orderItem->save();
                $totalAmount += $sku->price * $item['amount'];
                //减库存
                if ($sku->decreaseStock($item['amount']) <= 0) {
                    //如果减库存失败则抛出异常，由于这块代码是在 DB::transaction() 中执行的，
                    //因此抛出异常时会触发事务的回滚，之前创建的 orders 和 order_items 记录都会被撤销。
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            //更新订单总额
            $order->update(['total_amount'=>$totalAmount]);

            //将下单的商品从购物车移除
            $skuIds = collect($items)->pluck('id');
            $user->cartItems()->whereIn('product_sku_id',$skuIds)->delete();

            return $order;
        });

        return $order;

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
