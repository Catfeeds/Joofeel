<?php

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

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => '\Api\v3',
    'prefix' => 'enter',
    'middleware' => 'token'
],function(){
    /**
     * 商户
     */
    Route::group([
        'prefix' => 'merchants'
    ],function (){
        Route::get('get',      'MerchantsController@get');
        Route::get('search',   'MerchantsController@search');
        Route::get('push',     'MerchantsController@push');
        Route::get('order',    'MerchantsController@order');
        Route::get('ticket',   'MerchantsController@ticket');
        Route::post('operate', 'MerchantsController@operate');
    });

    /**
     *推送
     */
    Route::group([
        'prefix' => 'push'
    ],function(){
        Route::get('get',    'PushController@get');
        Route::get('search', 'PushController@search');
    });

    /**
     *商品
     */
    Route::group([
        'prefix' => 'ticket'
    ],function(){
        Route::get('get',    'TicketController@get');
        Route::get('search', 'TicketController@search');
    });

    /**
     *订单
     */
    Route::group([
        'prefix' => 'order'
    ],function(){
        Route::get('get',    'TicketOrderController@get');
        Route::get('search', 'TicketOrderController@search');
    });

    /**
     * 商户注册
     */
    Route::post('reg',   'AuthController@reg');

    /**
     *公众号模块商品
     */
    Route::group(
        [
            'prefix' => 'official'
        ],function (){
        Route::get('get',     'OfficialGoodsController@get');
        Route::post('add',    'OfficialGoodsController@add');
        Route::post('upload', 'OfficialGoodsController@upload');
    });
});
