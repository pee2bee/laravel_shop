<?php

namespace App\Services;

use App\Models\CartItem;

class CartService {
    public function get() {
        $user      = \Auth::user();
        $cartItems = $user->cartItems()->with( 'productSku' )->get();


        return $cartItems;
    }

    public function add( $product_sku_id, $amount ) {
        $user = \Auth::user();
        if ( $cart_item = $user->cartItems()->where( 'product_sku_id', '=', $product_sku_id )->first() ) {

            $cart_item->update( [
                'amount' => $cart_item->amount + $amount
            ] );
        } else {
            $cart_item                 = new CartItem();
            $cart_item->user_id        = $user->id;
            $cart_item->product_sku_id = $product_sku_id;
            $cart_item->amount         = $amount;
            $cart_item->save();
        }
    }

    public function remove( $ids ) {
        if ( ! is_array( $ids ) ) {
            //可以传单个id值，也可以传数组，如果是单个id值，转为数组
            $ids = [ $ids ];
        }

        \Auth::user()->cartItems()->whereIn( 'product_sku_id', $ids )->delete();
    }
}
