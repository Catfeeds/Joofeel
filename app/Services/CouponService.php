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
    public function add($data)
    {
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);
        Coupon::create($data);
    }

    public function all($limit)
    {
        $data = Coupon::orderByDesc('start_time')
                      ->select('id','sale','rule','species','category','day',
                          'count','start_time','end_time','isReceive')
                      ->paginate($limit);
        foreach ($data as $item)
        {
            $item['start_time'] = date('Y-m-d H:i',$item['start_time']);
            $item['end_time'] = date('Y-m-d H:i',$item['end_time']);
        }
        return $data;
    }
}