<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/6
 * Time: 17:31
 */
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'goods',
        'namespace' => 'Api\v1',
        'middleware' => 'auth'
    ],function(){
    Route::get('recommend','GoodsController@recommend');
});