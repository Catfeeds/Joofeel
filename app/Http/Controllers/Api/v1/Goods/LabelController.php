<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 13:14
 */

namespace App\Http\Controllers\Api\v1\Goods;

use App\Http\Controllers\Controller;
use App\Models\Goods\GoodsLabel;
use App\Utils\ResponseUtil;

class LabelController extends Controller
{
    public function get()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.goods,id'
            ]);
        $data = GoodsLabel::where('goods_id',$this->request->input('id'))
                          ->get();
        return ResponseUtil::toJson($data);
    }
}