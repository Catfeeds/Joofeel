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

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(
        [
            'middleware' => 'token',

        ], function ($api) {
        /**
         * 商品
         */
        $api->group(
            [
                'namespace'  => 'App\Http\Controllers\Api\v1\Goods',
                'prefix' => 'goods'
            ], function ($api) {
            $api->get('info',      'GoodsController@info');
            $api->get('category',  'GoodsController@category');
            $api->get('search',    'GoodsController@search');
            $api->get('oos',       'GoodsController@oos');
            $api->get('failure',   'GoodsController@failure');
            $api->post('operate',  'GoodsController@operate');
            $api->post('update',   'GoodsController@update');
            $api->post('excel',    'GoodsController@excel');
            $api->group(
                [
                    'prefix' => 'recommend'
                ], function ($api) {
                $api->get('',         'RecommendController@recommend');
                $api->post('operate', 'RecommendController@operate');
            });
        });
        /**
         * 抽奖
         */
        $api->group(
            [
                'namespace'  => 'App\Http\Controllers\Api\v1',
                'prefix' => 'prize'
            ], function ($api) {
            $api->post('',   'PrizeController@prize');
        });

    });

    /**
     * 管理员
     */
    $api->group(
        [
            'namespace' => 'App\Http\Controllers\Auth'
        ], function ($api) {
        /**
         * 需要判定是否登陆
         */
        $api->group(
            [
                'middleware' => 'token',
                'prefix'     => 'admin'
            ], function ($api) {
            $api->get('info',       'AuthController@info');
            $api->post('info',      'AuthController@updateInfo');
            $api->post('updatePwd', 'AuthController@updatePwd');
            $api->post('reg',       'AuthController@reg');
        });

        $api->group(
            [
                'prefix' => 'admin'
            ], function ($api) {
            $api->post('login', 'AuthController@login');
        });
    });
});