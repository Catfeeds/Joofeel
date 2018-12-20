<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/14
 * Time: 12:06
 */

namespace App\Services\MiniProgram\Order;

use App\Exceptions\AppException;
use App\Models\MiniProgram\Order\GoodsOrder;
use App\Models\MiniProgram\User\User;
use App\Services\MiniProgram\Message;

class OrderService
{
    /**
     * @param $sign
     * @param $limit
     * @return mixed
     * 判断是否要找未发货的订单
     */
    public function get($sign,$limit)
    {
        switch ($sign)
        {
            case 0:
                $order = $this->query()
                              ->where('isSign',GoodsOrder::NOTDELIVERY)
                              ->paginate($limit);
                break;
            case 3:
                $order = $this->query()
                              ->where('isSign',GoodsOrder::DELIVERIED)
                              ->paginate($limit);
                break;
            case 1:
                $order = $this->query()->paginate($limit);
                break;
            case 2:
                $order = GoodsOrder::where('isPay',GoodsOrder::UNPAID)
                                   ->where('created_at','>',date("Y-m-d H:i:s",strtotime("-1 day")))
                                   ->orderByDesc('updated_at')
                                   ->paginate($limit);
                break;
            case 4:
                $order = GoodsOrder::where('isPay',GoodsOrder::REFUND)
                                   ->orderByDesc('updated_at')
                                   ->paginate($limit);
                break;
        }
        return $order;
    }

    private function query()
    {
        $query = GoodsOrder::where('isPay',GoodsOrder::PAID)
                           ->orderByDesc('created_at');
        return $query;
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索
     */
    public function search($content,$limit)
    {
        $data = GoodsOrder::leftJoin('user as u','u.id','=','goods_order.user_id')
                          ->where('goods_order.order_id','like','%'.$content.'%')
                          ->orWhere('goods_order.receipt_name','like','%'.$content.'%')
                          ->orWhere('u.nickname','like','%'.$content.'%')
                          ->select('goods_order.*')
                          ->paginate($limit);
        return $data;
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
        if($order['tracking_id'])
        {
            $order['isSign'] = GoodsOrder::DELIVERIED;
            $order->save();
            $user = User::where('id',$order['user_id'])
                        ->select('openid')
                        ->first();
            (new Message())->sendOrderMessage($order,$user['openid']);
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
                                   $query->select('thu_url','id','sale_price','name');
                               }]);
                       }])
                           ->with('user')
                           ->select('id','order_id','tracking_id','price',
                               'sale_price','sale','receipt_name','receipt_address',
                               'receipt_phone','user_id','tracking_company')
                           ->where('id',$id)
                           ->first();
        foreach ($order['goods'] as $item)
        {
            $item['thu_url'] = $item['goods']['thu_url'];
            $item['price'] = $item['goods']['sale_price'];
            $item['name'] = $item['goods']['name'];
            unset($item['id'],$item['order_id'],$item['goods_id'],$item['goods']);
        }
        return $order;
    }

    /**
     * @param $id
     * @param $tracking_id
     * @param $tracking_company
     */
    public function update($id,$tracking_id,$tracking_company)
    {
        GoodsOrder::where('id',$id)
                  ->update([
                      'tracking_id' => $tracking_id,
                      'tracking_company' => $tracking_company
                  ]);
    }

}