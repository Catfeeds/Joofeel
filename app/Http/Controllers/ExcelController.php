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
     * 商品分类表
     */
    public function goodsCategory()
    {
        (new Excel)->sqlGoodsCategory((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 商品标签表
     */
    public function goodsLabel()
    {
        (new Excel)->sqlGoodsLabel((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 订单表
     */
    public function goodsOrder()
    {
        (new Excel)->sqlGoodsOrder((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 聚会留言表
     */
    public function message()
    {
        (new Excel)->sqlMessage((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     *订单详情
     */
    public function orderId()
    {
        (new Excel)->sqlOrderId((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 派对
     */
    public function party()
    {
        (new Excel)->sqlParty((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 排队订单表
     */
    public function partyOrder()
    {
        (new Excel)->sqlPartyOrder((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 排队订单表
     */
    public function prize()
    {
        (new Excel)->sqlPrize((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 排队订单表
     */
    public function prizeOrder()
    {
        (new Excel)->sqlPrizeOrder((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 排队订单表
     */
    public function recommend()
    {
        (new Excel)->sqlRecommend((new ExcelToArray())->getExcel());
        return ResponseUtil::toJson('','导入成功');
    }
}