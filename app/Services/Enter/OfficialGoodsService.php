<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 9:40
 */

namespace App\Services\Enter;


use App\Models\Enter\OfficialGoods;

class OfficialGoodsService
{
    /**
     * @param $thu_url
     * @param $title
     * @param $price
     * @param $url
     * @param $end_time
     * 新增
     */
    public function add($thu_url,$title,$price,$url,$end_time)
    {
        OfficialGoods::create(
            [
                'thu_url'  => $thu_url,
                'title'    => $title,
                'price'    => $price,
                'url'      => $url,
                'end_time' => $end_time
            ]);
    }
}