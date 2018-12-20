<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 14:11
 */

namespace App\Services\Enter;

use App\Models\Enter\Merchants;
use App\Models\Enter\Push;
use App\Models\Enter\Ticket;
use App\Models\Enter\TicketOrder;
use App\Models\MiniProgram\User\User;

class MerchantsService
{
    public function get($limit)
    {
        $data = Merchants::query()
                         ->paginate($limit);
        return $data;
    }

    /**
     * @param $content
     * @param $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 搜索商铺
     */
    public function search($content,$limit)
    {
        $data = Merchants::query()
                         ->where('merchants_name','like','%'.$content.'%')
                         ->paginate($limit);
        return $data;
    }

    /**
     * @param $id
     * 对商铺进行操作
     */
    public function operate($id)
    {
        $merchants = Merchants::where('id',$id)->first();
        if($merchants['is_ban'] == Merchants::BAN)
        {
            $merchants['is_ban'] = Merchants::NOT_BAN;
        }
        else
        {
            $merchants['is_ban'] = Merchants::BAN;
        }
        $merchants->save();
    }

    /**
     * @param $id
     * @param $limit
     * @return mixed
     * 获取商铺订单
     */
    public function order($id,$limit)
    {
        $data = TicketOrder::getMerchantsOrder($id,$limit);
        foreach ($data as $item)
        {
            $user = User::getUser($item['user_id']);
            $item['avatar'] = $user['avatar'];
            $item['nickname'] = $user['nickname'];
        }
        return $data;
    }

    /**
     * @param $id
     * @param $limit
     * @return mixed
     * 获取商铺票
     */
    public function ticket($id,$limit)
    {
        $data = Ticket::getMerchantsTicket($id,$limit);
        return $data;
    }

    /**
     * @param $id
     * @param $limit
     * @return mixed
     * 获取商铺推送
     */
    public function push($id,$limit)
    {
        $data = Push::getMerchantsPush($id,$limit);
        return $data;
    }
}