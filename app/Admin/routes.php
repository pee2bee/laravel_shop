<?php

use Illuminate\Routing\Router;


Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    /*$router->get('users','UsersController@index')->name('admin.users.index');
    $router->get('users/{user}','UsersController@show')->name('admin.users.show');
    $router->get('users/{user}/edit','UsersController@edit')->name('admin.users.edit');
    $router->put('users/{user}','UsersController@update')->name('admin.users.update');
    $router->post('users','UsersController@store')->name('admin.users.store');
    $router->delete('users/{user}','UsersController@destroy')->name('admin.users.destroy');*/
    $router->resource('users',UsersController::class);
    $router->resource('products', ProductsController::class);
});
