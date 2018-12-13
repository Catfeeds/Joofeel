<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/30
 * Time: 10:06
 */

namespace App\Services;

use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\Party\Party;
use App\Models\Party\PartyOrder;
use App\Models\User\DeliveryAddress;
use App\Models\User\User;
use App\Models\User\UserCoupon;
use App\Utils\Common;

const dayTimeStamp = 86400;

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
}