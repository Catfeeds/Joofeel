<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:50
 */

namespace App\Models\Coupon;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    //优惠券目前是否能被领取
    const CAN_RECEIVE = 0;
    const CAN_NOT_RECEIVE = 1;

    const NOT_POINT = 0;
    const POINT = 1;

    protected $table = 'coupon';

    public $timestamps = false;

    protected $fillable =
        [
            'rule',
            'sale',
            'species',
            'category',
            'day',
            'count',
            'isReceive',
            'isPoint',
            'start_time',
            'end_time'
        ];
}