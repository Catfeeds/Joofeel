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
            //商品详情
            $api->get('info',      'GoodsController@info');
            //分类下的所有商品
            $api->get('category',  'GoodsController@category');
            //搜索
            $api->get('search',    'GoodsController@search');
            //库存紧张的商品
            $api->get('oos',       'GoodsController@oos');
            //失效的商品
            $api->get('failure',   'GoodsController@failure');
            //上下架
            $api->post('operate',  'GoodsController@operate');
            //修改商品信息
            $api->post('update',   'GoodsController@update');
            //Excel添加商品
            $api->post('excel',    'GoodsController@excel');
            //Excel添加商品图
            $api->post('image',    'GoodsController@excelImage');
            /**
             * 审核
             */
            $api->group(
                [
                    'prefix' => 'pending'
                ], function ($api) {
                $api->get('',         'GoodsController@pending');
                $api->post('operate', 'GoodsController@pendingOperate');
            });
            /**
             * 推荐
             */
            $api->group(
                [
                    'prefix' => 'recommend'
                ], function ($api) {
                $api->get('',         'RecommendController@recommend');
                $api->post('operate', 'RecommendController@operate');
                $api->post('order',   'RecommendController@order');
            });
            /**
             * 推荐
             */
            $api->group(
                [
                    'prefix' => 'label'
                ], function ($api) {
                $api->get('get',       'LabelController@get');
                $api->post('update',   'LabelController@update');
                $api->post('add',      'LabelController@add');
                $api->delete('delete', 'LabelController@delete');
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
            $api->get('',        'PrizeController@record');
            $api->get('info',    'PrizeController@info');
            $api->post('',       'PrizeController@prize');
            $api->post('open',   'PrizeController@open');

        });

        /**
         * 订单
         */
        $api->group(
            [
                'namespace'  => 'App\Http\Controllers\Api\v1\Order',
                'prefix' => 'order'
            ],function ($api){
            //获取不同状态下的订单
            $api->get('',          'OrderController@get');
            $api->get('search',    'OrderController@search');
            //订单详情
            $api->get('info',      'OrderController@info');
            //发货操作
            $api->post('delivery', 'OrderController@delivery');

            /**
             * 修改订单号
             */
            $api->group([
                'prefix'    => 'update'
            ],function ($api){
                //单个修改
                $api->post('',      'OrderController@update');
                //excel表修改
                $api->post('excel', 'OrderController@updateExcel');
            });
            //获取订单的Excel(未发货,全部)
            $api->get('excel',     'OrderController@orderExcel');

            /**
             * 退款
             */
            $api->group([
                'prefix'    => 'refund'
            ],function ($api){
                $api->get('get',     'RefundController@get');
                $api->post('refuse', 'RefundController@refuse');
                $api->post('agree',  'RefundController@agree');
            });
        });

        /**
         * 首页数据
         */
        $api->group(
            [
                'namespace' => 'App\Http\Controllers\Api\v1',
                'prefix'    => 'index'
            ],function($api){
            $api->get('sale',   'IndexController@salePercent');
            $api->get('user',   'IndexController@recentUser');
            $api->get('todo',   'IndexController@todo');
        });

        /**
         * 优惠券
         */
        $api->group(
            [
                'namespace' => 'App\Http\Controllers\Api\v1',
                'prefix'    => 'coupon'
            ],function($api){
            $api->get('all',       'CouponController@all');
            $api->post('add',      'CouponController@add');
            //下加优惠券
            $api->post('operate',  'CouponController@operate');
        });

        /**
         * Banner
         */
        $api->group(
            [
                'namespace' => 'App\Http\Controllers\Api\v1',
                'prefix'    => 'banner'
            ],function($api){
            $api->get('',          'BannerController@get');
            $api->get('all',       'BannerController@all');
            $api->post('add',      'BannerController@add');
            $api->post('update',   'BannerController@update');
            $api->post('operate',  'BannerController@operate');
            $api->post('upload',   'BannerController@upload');
        });

        /**
         * 用户
         */
        $api->group(
            [
                'namespace' => 'App\Http\Controllers\Api\v1',
                'prefix'    => 'user'
            ],function($api){
            $api->get('',          'UserController@get');
        });


        /**
         * 配置API
         */
        $api->group(
            [
                'namespace' => 'App\Http\Controllers\Api\v1\Config',
                'prefix'    => 'config'
            ],function($api){
                $api->group([
                    'prefix' => 'search'
                ],function ($api){
                    $api->get('get',       'SearchController@get');
                    $api->post('add',      'SearchController@add');
                    $api->post('update',   'SearchController@update');
                    $api->delete('delete', 'SearchController@delete');
                });

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
            $api->get('',           'AdminController@get');
            $api->get('info',       'AuthController@info');
            $api->post('info',      'AuthController@updateInfo');
            $api->post('updatePwd', 'AuthController@updatePwd');
            $api->post('reg',       'AuthController@reg');
            $api->post('ban',       'AdminController@ban');
            $api->post('set',       'AdminController@set');
        });

        $api->group(
            [
                'prefix' => 'admin'
            ], function ($api) {
            $api->post('login', 'AuthController@login');
            $api->group(
                [
                    'middleware' => 'token',
                    'prefix'     => 'reg'
                ],function ($api){
                $api->post('',   'AuthController@reg');
            });

        });
    });
});