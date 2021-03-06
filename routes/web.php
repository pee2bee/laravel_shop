<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*首页路由*/
Route::redirect( '/', '/products' )->name( 'root' );


/*登录认证相关路由，开启邮箱验证路由*/
Auth::routes( [ 'verify' => true ] );

/*需要auth认证的路由*/
Route::group( [ 'middleware' => [ 'auth', 'verified' ] ], function () {

    //用户地址
    Route::get( 'addresses', 'AddressesController@index' )->name( 'addresses.index' );
    Route::get( 'addresses/create', 'AddressesController@create' )->name( 'addresses.create' );
    Route::post( 'addresses', 'AddressesController@store' )->name( 'addresses.store' );
    Route::delete( 'addresses/{address}', 'AddressesController@destroy' )->name( 'addresses.destroy' );
    Route::get( 'addresses/{address}', 'AddressesController@edit' )->name( 'addresses.edit' );
    Route::patch( 'addresses/{address}', 'AddressesController@update' )->name( 'addresses.update' );

    //收藏
    Route::post( 'products/{product}/favorite', 'ProductsController@favor' )->name( 'products.favor' );
    Route::delete( 'products/{product}/favorite', 'ProductsController@disfavor' )->name( 'products.disfavor' );
    Route::get( 'products/favorites', 'ProductsController@favorites' )->name( 'products.favorites' );

    //购物车
    Route::get( 'cartItems', 'CartItemsController@index' )->name( 'cart.index' );
    Route::post( 'cartItems', 'CartItemsController@store' )->name( 'cart.store' );
    Route::delete( 'cartItems/{cartItem}', 'CartItemsController@destroy' )->name( 'cart.destroy' );

    //下订单
    Route::post( 'orders', 'OrdersController@store' )->name( 'orders.store' );
    //用户订单列表
    Route::get( 'orders', 'OrdersController@index' )->name( 'orders.index' );
    //订单详情
    Route::get( 'orders/{order}', 'OrdersController@show' )->name( 'orders.show' );
    //表注订单为已收货
    Route::post( 'orders/{order}/received', 'OrdersController@received' )->name( 'orders.received' );
    //订单评价视图
    Route::get( 'orders/{order}/review', 'OrdersController@review' )->name( 'orders.review.show' );
    //订单评价保存
    Route::post( 'orders/{order}/review', 'OrdersController@sendReview' )->name( 'orders.review.store' );
    //订单退款申请
    Route::post( 'orders/{order}/applyRefund', 'OrdersController@applyRefund' )->name( 'orders.refund.apply' );

    //支付宝支付
    Route::get( 'payment/{order}/alipay', 'PaymentController@payByAlipay' )->name( 'payment.alipay' )->where( [ 'order' => '[0-9]+' ] );
    //支付后前端回调
    Route::get( 'payment/alipay/return', 'PaymentController@alipayReturn' )->name( 'payment.alipay.return' );

    //检查优惠券
    Route::post( 'coupons/check', 'CouponsController@check' )->name( 'coupons.check' );
    //使用优惠券


} );

/*不需要auth认证的路由*/
Route::get( 'products', 'ProductsController@index' )->name( 'products.index' );
Route::get( 'products/{product}', 'ProductsController@show' )->name( 'products.show' )->where( [ 'product' => '[0-9]+' ] );


//支付后通知回调，支付宝自动向该路由发起请求
//需要忽略csrf验证
Route::post( 'payment/alipay/notify', 'PaymentController@alipayNotify' )->name( 'payment.alipay.notify' );




