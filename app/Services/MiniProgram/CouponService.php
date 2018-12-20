<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 15:19
 */

namespace App\Services\MiniProgram;

use App\Models\MiniProgram\Coupon\Coupon;
use App\Models\MiniProgram\User\User;
use App\Models\MiniProgram\User\UserCoupon;

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
                          'count','start_time','end_time','isReceive','isPoint')
                      ->paginate($limit);
        foreach ($data as $item)
        {
            $item['start_time'] = date('Y-m-d H:i',$item['start_time']);
            $item['end_time'] = date('Y-m-d H:i',$item['end_time']);
        }
        return $data;
    }

    /**
     * @param $id
     * 下架优惠券
     */
    public function operate($id)
    {
        $coupon = Coupon::where('id',$id)->first();
        if($coupon['isReceive'] == Coupon::CAN_RECEIVE)
        {
            $receive = Coupon::CAN_NOT_RECEIVE;
        }
        else
        {
            $receive = Coupon::CAN_RECEIVE;
        }
        Coupon::where('id',$id)
              ->update([
                  'isReceive' => $receive
              ]);
    }

    /**
     * @param $id
     * @return mixed
     * 发放优惠券
     */
    public function send($id)
    {
        $user = User::select('id')->get();
        foreach ($user as $item)
        {
            $record = UserCoupon::where('user_id',$item['id'])
                                ->where('coupon_id',$id)
                                ->first();
            if(!$record)
            {
                (new UserService())->sendCoupon($item['id'],$id);
            }
        }
    }

    public function update($id,$count)
    {
        Coupon::where('id',$id)
              ->update([
                  'count' => $count
              ]);
    }

}