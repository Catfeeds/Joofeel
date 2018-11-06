<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-10
 * Time: 15:18
 */

namespace App\Utils;


class ResponseUtil
{
    // 格式化返回数据
    static function toJson($data = [],$msg = 'OK', $code = 200)
    {
        return response()->json([
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ]);
    }
}