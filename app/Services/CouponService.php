<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 15:19
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Coupon\Coupon;
use App\Models\User\UserCoupon;
use App\Utils\Common;

class CouponService extends BaseService
{

    /**
     * @return mixed
     * 获取用户当前可以领取的优惠券
     */
    public function get()
    {
        $data = $this->query()
                     ->get()
                     ->toArray();
        foreach ($data as $key => $item) {
            $record = UserCoupon::getCoupon($item['id'],$this->uid);
            if ($record) {
                array_splice($data, $key, 1);
            }
        }
        $result = Common::getCouponCategory($data);
        return $result;
    }

    /**
     * @param $id
     * @throws AppException
     * 领取优惠券
     */
    public function receive($id)
    {
        $data = $this->query()
                     ->where('id',$id)
                     ->first();
        if($data)
        {
            $record = UserCoupon::getCoupon($id,$this->uid);
            if($record)
            {
                throw new AppException('不可以重复领取哦');
            }
            UserCoupon::create([
                'user_id'    => $this->uid,
                'coupon_id'  => $id,
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time']
            ]);
            return;
        }
        throw new AppException('暂时无法领取');
    }

    /**
     * @return mixed
     * 通用查询语句
     */
    private function query()
    {
        $query = Coupon::where('isReceive',Coupon::CAN_RECEIVE)
                       ->where('count','>',0)
                       ->where('end_time','>',time());
        return $query;
    }
}