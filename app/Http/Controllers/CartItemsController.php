<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = \request()->user();
        $cartItems = $user->cartItems()->with('productSku')->get();
        return view('users.cart', compact('cartItems'));
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
    public function store(CartItemRequest $request)
    {

        if ($user = $request->user()) {
            //查记录，如果已经存在，则添加数量
            //不存在，新增记录
            if ($cart_item = $user->cartItems()->where('product_sku_id','=',$request->product_sku_id)->first()) {

                $cart_item->update([
                    'amount' => $cart_item->amount + $request->amount
                ]);
            }else{
                $cart_item = new CartItem();
                $cart_item->user_id = $user->id;
                $cart_item->product_sku_id = $request->product_sku_id;
                $cart_item->amount = $request->amount;
                $cart_item->save();
            }
        }

        return [];
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
        \request()->user()->cartItems()->where('id',$id)->delete();
        return [];
    }
}
