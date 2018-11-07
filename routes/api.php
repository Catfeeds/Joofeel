<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->group(function (){
    Route::group(
        [
            'prefix' => 'admin',
            'namespace' => 'Auth'
        ],function(){
        Route::get('info',       'AuthController@info');
        // 修改密码
        Route::post('updatePwd', 'AuthController@updatePwd');

    });
});

$api = app('Dingo\Api\Routing\Router');

// 配置api版本和路由
$api->version(
    'v1',
    [
        'namespace' => 'App\Http\Controllers\Api\v1'
    ], function ($api) {
    $api->get("test", 'GoodsController@recommend');

});
