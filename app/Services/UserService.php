<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/30
 * Time: 10:06
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Coupon\Coupon;
use App\Models\Order\GoodsOrder;
use App\Models\User\User;
use App\Models\User\UserCoupon;
use App\Utils\Common;


define('DAY_TIMESTAMP',86400);

class UserService
{
    public function get($limit)
    {
        $user = User::withCount('host')
                    ->withCount('join')
                    ->where('nickname','!=','')
                    ->paginate($limit);
        return $this->getUserPrice($user);
    }

    /**
     * @param $user_id
     * @param $coupon_id
     * @throws AppException
     * 指定发送优惠券
     */
    public function sendCoupon($user_id,$coupon_id)
    {
        $coupon = $this->query()
                       ->where('id',$coupon_id)
                       ->first();
        if($coupon)
        {
            if($coupon['species'] == Coupon::FIXED)
            {
                $endTime = $coupon['end_time'];
            }
            else
            {
                //从当前时间开始计算
                $start = strtotime(date("Y-m-d"),time());
                $add = DAY_TIMESTAMP * $coupon['day'];
                $endTime = $start + $add + DAY_TIMESTAMP - 1; //当天的23:59:59
            }
            UserCoupon::create([
                'user_id'    => $user_id,
                'coupon_id'  => $coupon_id,
                'start_time' => time(),
                'end_time'   => $endTime
            ]);
            $coupon['count'] -= 1;
            $coupon->save();
        }
        else
        {
            throw new AppException('该优惠券无法派送,可能已下架');
        }
    }

    /**
     * @param $user_id
     * @return array|mixed
     * 获取用户可以领取的优惠券
     */
    public function getUserCoupon($user_id)
    {
        $data = $this->query()
                     ->where('end_time','>',time())
                     ->where('isPoint',Coupon::POINT)
                     ->get()
                     ->toArray();
        $result = array();
        foreach ($data as $key => $item) {
            $record = UserCoupon::getCoupon($item['id'], $user_id);
            if (!$record) {
                $data[$key]['start_time'] = date('Y-m-d H:i',$data[$key]['start_time']);
                $data[$key]['end_time'] = date('Y-m-d H:i',$data[$key]['end_time']);
                array_push($result, $data[$key]);
            }
        }
        $result = Common::getCouponCategory($result);
        return $result;
    }

    /**
     * @param $user
     * @return array
     * 得到用户的
     */
    private function getUserPrice($user)
    {
        foreach ($user as $item)
        {
            $order = GoodsOrder::where('user_id',$item['id'])
                               ->select('sale_price')
                               ->get();
            $item['price'] = 0;
            foreach ($order as $item_price)
            {
                $item['price'] = bcadd($item['price'],$item_price['sale_price'],1);
            }
        }
        return $user;
    }

    private function query()
    {
        $query = Coupon::where('isReceive',Coupon::CAN_RECEIVE)
                       ->where('count','>',0)
                       ->select('id','species','day','count','start_time','end_time',
                           'rule','sale','category','isPoint');
        return $query;
    }

}