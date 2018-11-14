<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/14
 * Time: 12:06
 */

namespace App\Services;


use App\Exceptions\AppException;
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
                               ->where('isPay',GoodsOrder::PAID)
                               ->orderByDesc('created_at')
                               ->paginate($limit);
            return $order;
        }
        else if($sign == 1)
        {
            $order = GoodsOrder::where('isPay',GoodsOrder::PAID)
                               ->orderByDesc('created_at')
                               ->paginate($limit);
            return $order;
        }
        $order = GoodsOrder::where('isPay',GoodsOrder::UNPAID)
                           ->where('created_at','>',date("Y-m-d H:i:s",strtotime("-1 day")))
                           ->orderByDesc('created_at')
                           ->paginate($limit);
        return $order;
    }

    /**
     * @param $id
     * @throws AppException
     * 发货
     */
    public function delivery($id)
    {
        $order=  GoodsOrder::where('id',$id)
                           ->first();
        if($order['tracking_id'] == GoodsOrder::NOTTRACKINGID)
        {
            throw new AppException('未填写快递单号,不能发货');
        }
        $order['isSign'] = GoodsOrder::DELIVERIED;
        $order->save();
    }
}