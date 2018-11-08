<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:47
 */

namespace App\Models\Goods;


use Illuminate\Database\Eloquent\Model;

class GoodsLabel extends Model
{
    protected $table = 'goods_label';

    protected $fillable = [
        'goods_id',
        'label_name'
    ];
}