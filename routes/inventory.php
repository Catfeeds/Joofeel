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
    'namespace' => '\Api\v2',
    'prefix' => 'inventory',
    'middleware' => 'token'
],function(){
    Route::get('get',             'InventoryController@get');
    Route::get('search',          'InventoryController@search');
    Route::post('add',            'InventoryController@add');
    Route::post('update',         'InventoryController@update');
    Route::get('outbound/get',    'InventoryController@getOutboundRecord');
    Route::get('outbound/search', 'InventoryController@searchOutbound');
});
