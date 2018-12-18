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

Route::group(['middleware' => 'web'], function () {
    Route::Auth();
    Route::group(
        [
            'prefix' => 'admin',
            'namespace' => 'Auth'
        ],function(){
        Route::post('login','AuthController@login');
        Route::get('logout','AuthController@logout');
        /**
         * 需要添加权限认证
         */
        Route::group(
            [
                'middleware' => 'auth'
            ], function (){
            // 查看个人信息
            Route::get('info',       'AuthController@info');
            // 修改密码
            Route::post('updatePwd', 'AuthController@updatePwd');
        }
        );
    });
});