<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 13:16
 */

namespace App\Services\Goods;


use App\Models\Goods\GoodsLabel;

class LabelService
{
    public function add($data)
    {
        GoodsLabel::create([
            'goods_id' => $data['id'],
            'label_name' => $data['label_name']
        ]);
    }

    public function update($data)
    {
        GoodsLabel::where('id',$data['id'])
                  ->update([
                      'label_name' => $data['label_name']
                  ]);
    }
}