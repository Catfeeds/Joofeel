<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:46
 */

namespace App\Models\Goods;


use Illuminate\Database\Eloquent\Model;

class GoodsCategory extends Model
{
    protected $table = 'goods_category';

    protected $fillable =
        [
            'id',
            'name'
        ];
    public $timestamps = false;
}