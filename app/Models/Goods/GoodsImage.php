<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/7
 * Time: 14:12
 */

namespace App\Models\Goods;


use Illuminate\Database\Eloquent\Model;

class GoodsImage extends Model
{
    protected $table= 'goods_image';

    protected $fillable = [
        'goods_id',
        'url',
        'order'
    ];

    public $timestamps = false;

}