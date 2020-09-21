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
Route::redirect('/', '/products');

/*登录认证相关路由，开启邮箱验证路由*/
Auth::routes(['verify' => true]);

/*需要auth认证的路由*/
Route::group(['middleware'=>['auth', 'verified']],function () {
    /*地址相关路由*/
   Route::get('addresses','AddressesController@index')->name('addresses.index');
   Route::get('addresses/create','AddressesController@create')->name('addresses.create');
   Route::post('addresses','AddressesController@store')->name('addresses.store');
   Route::delete('addresses/{address}','AddressesController@destroy')->name('addresses.destroy');
   Route::get('addresses/{address}','AddressesController@edit')->name('addresses.edit');
   Route::patch('addresses/{address}','AddressesController@update')->name('addresses.update');

   Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');
   Route::delete('products/{product}/favorite','ProductsController@disfavor')->name('products.disfavor');
   Route::get('products/favorites','ProductsController@favorites')->name('products.favorites');
});

/*不需要auth认证的路由*/
Route::get('products','ProductsController@index')->name('products.index');
Route::get('products/{product}','ProductsController@show')->name('products.show')->where(['product'=>'[0-9]+']);



