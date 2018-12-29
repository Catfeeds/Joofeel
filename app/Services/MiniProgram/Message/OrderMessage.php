<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/29
 * Time: 10:11
 */

namespace App\Services\MiniProgram\Message;

use App\Exceptions\AppException;
use App\Models\MiniProgram\Goods\Goods;
use App\Models\MiniProgram\Order\OrderId;
use App\Services\MiniProgram\Message\base\BaseMessage;

const ORDER_SELF_TEMPLATE_ID = 'thVitw3RNsJL8Zp9XJHoyol1qJL7UOBx3_E5EXMfyQg';
const ORDER_OTHER_TEMPLATE_ID = 'thVitw3RNsJL8Zp9XJHoyldQqz8PiSU9_E3isTmPJHU';


class OrderMessage extends BaseMessage
{
    public function sendOrder($order,$openId)
    {
        $record = OrderId::where('order_id',$order['id'])->get();
        if(count($record) == 1)
        {
            $goods = Goods::where('id',$record[0]['goods_id'])
                          ->select('name')
                          ->first();
            $this->data = [
                'keyword1' => [
                    'value' => '您在聚Feel小程序上购买的商品已发货,请注意查收'
                ],
                'keyword2' => [
                    'value' => $order['order_id']
                ],
                'keyword3' => [
                    'value' => $order['tracking_company']
                ],
                'keyword4' => [
                    'value' => $order['tracking_id']
                ],
                'keyword5' => [
                    'value' => '详询客服(工作日 10:00-18:00)'
                ],
                'keyword6' => [
                    'value' => $goods['name']
                ],
                'keyword7' => [
                    'value' => $order['receipt_address']
                ]
            ];
            $this->tplId = ORDER_SELF_TEMPLATE_ID;
        }
        else
        {
            $this->data = [
                'keyword1' => [
                    'value' => '您在聚Feel小程序上购买的商品已发货,请注意查收'
                ],
                'keyword2' => [
                    'value' => $order['order_id']
                ],
                'keyword3' => [
                    'value' => '详询客服(工作日 10:00-18:00)'
                ],
                'keyword4' => [
                    'value' => '详询客服(工作日 10:00-18:00)'
                ],
                'keyword5' => [
                    'value' => $order['receipt_address']
                ]
            ];
            $this->tplId = ORDER_OTHER_TEMPLATE_ID;
        }
        $this->page = '/pages/mystore/mystore';
        $result = $this->send($openId,$order['prepay_id']);
        if($result['errcode'] != 0)
        {
            throw new AppException($result['errmsg']);
        }
        return $result;
    }
}