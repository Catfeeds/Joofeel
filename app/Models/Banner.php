<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:46
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const SHOW = 0;

    const NOT_SHOW = 1;

    const GOODS_DETAIL = 1;
    const ACTIVITY_DETAIL = 2;
    const GOODS_CATEGORY = 3;

    protected $table = 'banner';

//    public $timestamps = false;

    protected $fillable =
        [
            'id',
            'type',
            'url',
            'goods_id',
            'isPrize',
            'isShow',
            'created_at',
            'updated_at'
        ];
}