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
        if($order['tracking_id'] != GoodsOrder::NOTTRACKINGID)
        {
            $order['isSign'] = GoodsOrder::DELIVERIED;
            $order->save();
        }
        else
        {
            throw new AppException('未填写快递单号,不能发货','');
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * 订单详情
     */
    public function info($id)
    {
        $order = GoodsOrder::with(['goods' =>function($query){
                           $query->select('id','order_id','goods_id')
                               ->with(['goods'=>function($query){
                                   $query->select('thu_url','id','price','name');
                               }]);
                       }])
                           ->with('user')
                           ->select('id','order_id','tracking_id','price',
                               'sale_price','sale','receipt_name','receipt_address',
                               'receipt_phone','user_id')
                           ->where('id',$id)
                           ->first();
        return $order;
    }

    /**
     * @param $id
     * @param $tracking_id
     * 单个修改订单信息(只能修改快递单号)
     */
    public function update($id,$tracking_id)
    {
        GoodsOrder::where('id',$id)
                  ->update([
                      'tracking_id' => $tracking_id
                  ]);
    }

    public function excelUpdate()
    {
        $res = (new ExcelToArray())->getExcel();
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
            (new ExcelToArray())->orderExcel($this->transOrderSign($data), '未发货订单');
        }
        else
        {
            $data = GoodsOrder::where('isPay',GoodsOrder::PAID)
                              ->orderByDesc('created_at')
                              ->get();
            (new ExcelToArray())->orderExcel($this->transOrderSign($data), '全部订单');
        }
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
            if($item['isSign'] = GoodsOrder::NOTDELIVERY)
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