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
Route::get('/', 'PagesController@root')->middleware(['verified']);

/*登录认证相关路由，开启邮箱验证路由*/
Auth::routes(['verify' => true]);

Route::group(['middleware'=>['auth', 'verified']],function () {
    /*地址相关路由*/
   Route::get('addresses','AddressesController@index')->name('addresses.index');
   Route::get('addresses/create','AddressesController@create')->name('addresses.create');
   Route::post('addresses','AddressesController@store')->name('addresses.store');
   Route::delete('addresses/{address}','AddressesController@destroy')->name('addresses.destroy');
   Route::get('addresses/{address}','AddressesController@edit')->name('addresses.edit');
   Route::patch('addresses/{address}','AddressesController@update')->name('addresses.update');
});



