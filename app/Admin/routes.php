<?php

use Illuminate\Routing\Router;


Admin::routes();

Route::group( [
    'prefix'     => config( 'admin.route.prefix' ),
    'namespace'  => config( 'admin.route.namespace' ),
    'middleware' => config( 'admin.route.middleware' ),
], function ( Router $router ) {

    $router->get( '/', 'HomeController@index' )->name( 'admin.home' );
    /*$router->get('products.,'UsersController@index')->name('admin.users.index');
    $router->get('users/{user}','UsersController@show')->name('admin.users.show');
    $router->get('users/{user}/edit','UsersController@edit')->name('admin.users.edit');
    $router->put('users/{user}','UsersController@update')->name('admin.users.update');
    $router->post('users','UsersController@store')->name('admin.users.store');
    $router->delete('users/{user}','UsersController@destroy')->name('admin.users.destroy');*/
    //用户管理
    $router->resource( 'users', UsersController::class )->names( [
        'index'   => 'admin.users.index',
        'create'  => 'admin.users.create',
        'store'   => 'admin.users.store',
        'edit'    => 'admin.users.edit',
        'show'    => 'admin.users.show',
        'update'  => 'admin.users.update',
        'destroy' => 'admin.uses.destroy'
    ] );
    //商品管理
    $router->resource( 'products', ProductsController::class )->names( [
        'index'   => 'admin.products.index',
        'create'  => 'admin.products.create',
        'store'   => 'admin.products.store',
        'edit'    => 'admin.products.edit',
        'show'    => 'admin.products.show',
        'update'  => 'admin.products.update',
        'destroy' => 'admin.products.destroy'
    ] );
    //订单管理
    $router->resource( 'orders', OrdersController::class )->names( [
        'index'   => 'admin.orders.index',
        'create'  => 'admin.orders.create',
        'store'   => 'admin.orders.store',
        'edit'    => 'admin.orders.edit',
        'show'    => 'admin.orders.show',
        'update'  => 'admin.orders.update',
        'destroy' => 'admin.orders.destroy'
    ] );
    $router->post( 'orders/{order}/ship', 'OrdersController@ship' )->name( 'admin.orders.ship' );
    $router->post( 'orders/{order}/agreeRefund', 'OrdersController@agreeRefund' )
           ->name( 'admin.orders.agree_refund' );
    $router->post( 'orders/{order}/disagreeRefund', 'OrdersController@disagreeRefund' )
           ->name( 'admin.orders.disagree_refund' );
    //优惠券管理
    $router->resource( 'coupons', CouponsController::class )->names( [
        'index'   => 'admin.coupons.index',
        'create'  => 'admin.coupons.create',
        'store'   => 'admin.coupons.store',
        'edit'    => 'admin.coupons.edit',
        'update'  => 'admin.coupons.update',
        'destroy' => 'admin.coupons.destroy'
    ] );


} );
