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
        $res = (new ExcelToArray())->getExcel();
        (new Excel)->sqlGoods($res);
        return ResponseUtil::toJson('','导入成功');
    }
}