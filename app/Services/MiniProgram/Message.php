<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/6
 * Time: 14:09
 */

namespace App\Services\MiniProgram;

use App\Exceptions\AppException;
use App\Models\MiniProgram\Goods\Goods;
use App\Models\MiniProgram\Order\OrderId;
use EasyWeChat\Factory;

const ORDER_SELF_TEMPLATE_ID = 'thVitw3RNsJL8Zp9XJHoyol1qJL7UOBx3_E5EXMfyQg';
const ORDER_OTHER_TEMPLATE_ID = 'thVitw3RNsJL8Zp9XJHoyldQqz8PiSU9_E3isTmPJHU';
const PRIZE_TEMPLATE_ID = 'V4jut_BN7CVL4xQ-VV7k28H3h9eIjRlD030njTKFGMI';


class Message
{
    private $app;
    public function __construct()
    {
        $config = config('wechat.mini_program.default');
        $this->app = Factory::miniProgram($config);
    }

    /**
     * @param $record
     * @param $id
     * @throws AppException
     */
    public function sendPrizeMessage($record,$id)
    {
        foreach ($record as $item)
        {
            $data = $this->app->template_message->send([
                'touser' => $item['user']['openid'],
                'template_id' => PRIZE_TEMPLATE_ID,
                'page' => '/pages/shishouqi2/shishouqi2?id=' . $id,
                'form_id' => $item['form_id'],
                'data' => [
                    'keyword1' => [
                        'value' => '你参加的抽奖已经开奖，点击查看幸运锦鲤是不是你'
                    ],
                    'keyword2' => [
                        'value' => '如果你是幸运锦鲤，请通过小程序“我的-联系客服”领奖吧'
                    ],
                    'keyword3' => [
                        'value' => '请于开奖后3个工作日内联系客服领奖，逾期作废呢'
                    ],
                ],
            ]);
            if($data['errcode'] != 0)
            {
                throw new AppException($data['errmsg']);
            }
        }
    }


    public function sendOrderMessage($order,$openId)
    {
        $record = OrderId::where('order_id',$order['id'])->get();
        if(count($record) == 1)
        {
            $goods = Goods::where('id',$record[0]['goods_id'])
                          ->select('name')
                          ->first();
            $data = [
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
            $tpl_id = ORDER_SELF_TEMPLATE_ID;
        }
        else
        {
            $data = [
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
            $tpl_id = ORDER_OTHER_TEMPLATE_ID;
        }
        $result = $this->app->template_message->send([
            'touser' =>$openId,
            'template_id' => $tpl_id,
            'page' => '/pages/mystore/mystore',
            'form_id' => $order['prepay_id'],
            'data' => $data
        ]);
        if($result['errcode'] != 0)
        {
            throw new AppException($result['errmsg']);
        }
        return $result;
    }

}