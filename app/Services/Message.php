<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/6
 * Time: 14:09
 */

namespace App\Services;

use EasyWeChat\Factory;


class Message
{
    private $app;
    public function __construct()
    {
        $config = config('wechat.payment.default');
        $this->app = Factory::miniProgram($config);
    }

    public function sendPrizeMessage($record,$id)
    {
        foreach ($record as $item)
        {
            $this->app->template_message->send([
                'touser' => $item['user']['openid'],
                'template_id' => 'V4jut_BN7CVL4xQ-VV7k28H3h9eIjRlD030njTKFGMI-id',
                'url' => '/pages/shishouqi2/shishouqi2?id=' . $id,
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
        }

    }
}