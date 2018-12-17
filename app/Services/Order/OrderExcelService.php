<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 16:04
 */

namespace App\Services\Order;

use App\Models\Goods\Goods;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Services\ExcelToArray;

class OrderExcelService
{
    /**
     * 通过订单号修改信息
     */
    public function updateExcel()
    {
        $res = (new ExcelToArray())->get();
        foreach ($res as $k => $v) {
            if ($k > 1) {
                $order = GoodsOrder::where('order_id',$v[0])
                                   ->first();
                $order['tracking_id'] = $v[1];
                $order['tracking_company'] = $v[2];
                $order->save();
            }
        }
    }

    /**
     * @param $sign
     * 获取订单的excel
     */
    public function getOrderExcel($sign)
    {
        if($sign == GoodsOrder::NOTDELIVERY)
        {
            $data = GoodsOrder::where('isPay',GoodsOrder::PAID)
                              ->where('isSign',GoodsOrder::NOTDELIVERY)
                              ->get();
            $name = '未发货订单';
        }
        else
        {
            $data = GoodsOrder::where('isPay',GoodsOrder::PAID)
                              ->orderByDesc('created_at')
                              ->get();
            $name = '全部订单';
        }
        (new ExcelToArray())->order($this->transOrderSign($data), $name,$this->orderRecord($data));
    }

    /**
     * @param $data
     * @return array
     * 获取订单中商品信息g
     */
    private function orderRecord($data)
    {
        $info = array();
        foreach ($data as $order)
        {
            $singleInfo = array();
            $single = OrderId::where('order_id',$order['id'])->get();
            foreach ($single as  $item)
            {
                $goods = Goods::where('id',$item['goods_id'])->first();
                $record[0]  = $goods['name'];
                $record[1] = $item['count'];
                array_push($singleInfo,$record);
            }
            array_push($info,$singleInfo);
        }
        return $info;
    }

    /**
     * @param $data
     * @return mixed
     * 转换状态
     */
    private function transOrderSign($data)
    {
        foreach ($data as $item)
        {
            if($item['isSign'] == GoodsOrder::NOTDELIVERY)
            {
                $item['isSign'] = '未发货';
            }
            else
            {
                $item['isSign'] = '已发货';
            }
        }
        return $data;
    }

}