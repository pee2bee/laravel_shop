<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemsController extends Controller {

    protected $cart_service;

    public function __construct( CartService $cart_service ) {
        $this->cart_service = $cart_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
        $user      = \request()->user();
        $cartItems = $this->cart_service->get();
        $addresses = $user->addresses()->orderBy( 'last_used_at', 'desc' )->get();

        return view( 'users.cart', compact( 'cartItems', 'addresses' ) );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store( CartItemRequest $request ) {

        if ( $user = $request->user() ) {
            //查记录，如果已经存在，则添加数量
            //不存在，新增记录
            $this->cart_service->add( $request->product_sku_id, $request->amount );
        }

        return [];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id ) {
        //
        $this->cart_service->remove( $id );

        return [];
    }
}
