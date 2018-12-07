<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 15:19
 */

namespace App\Services;

use App\Models\Coupon\Coupon;

class CouponService
{
    public function add()
    {

    }

    public function all()
    {
        $data = Coupon::orderByDesc('start_time')
                      ->select('id','sale','rule','species','category','day',
                          'count','start_time','end_time','isReceive')
                      ->get();
        return $data;
    }
}