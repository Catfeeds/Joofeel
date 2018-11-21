<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/16
 * Time: 14:03
 */

namespace App\Http\Controllers;

use App\Services\ExcelToArray;
use App\Services\Excel\MysqlExcel as Excel;
use App\Utils\ResponseUtil;

class ExcelController extends Controller
{

    /**
     *商品表
     */
    public function goods()
    {
        (new Excel)->sqlGoods((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * banner表
     */
    public function banner()
    {
        (new Excel)->sqlBanner((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 购物券表
     */
    public function coupon()
    {
        (new Excel)->sqlCoupon((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 收货地址
     */
    public function address()
    {
        (new Excel)->sqlDeliveryAddress((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

}