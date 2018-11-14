<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/14
 * Time: 12:06
 */

namespace App\Services;


use App\Http\Controllers\Controller;
use App\Models\Order\GoodsOrder;

class OrderService extends Controller
{
    /**
     * @param $sign
     * @param $limit
     * @return mixed
     * 判断是否要找未发货的订单
     */
    public function get($sign,$limit)
    {
        if($sign == 0)
        {
            $order = GoodsOrder::where('isSign',$sign)
                               ->orderByDesc('create_at')
                               ->paginate($limit);
            return $order;
        }
        $order = GoodsOrder::orderByDesc('create_at')
                           ->paginate($limit);
        return $order;
    }
}