<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/29
 * Time: 10:06
 */

namespace App\Services\MiniProgram\Message;

use App\Exceptions\AppException;
use App\Services\MiniProgram\Message\base\BaseMessage;

const TEMPLATE_MESSAGE_ID = 'V4jut_BN7CVL4xQ-VV7k28H3h9eIjRlD030njTKFGMI';

class PrizeMessage extends BaseMessage
{
    /**
     * @param $record
     * @param $id
     * @throws AppException
     */
    public function sendPrize($record,$id)
    {
        $this->tplId = TEMPLATE_MESSAGE_ID;
        $this->page  = '/pages/shishouqi2/shishouqi2?id=' . $id;
        $this->data  = [
            'keyword1' => [
                'value' => '你参加的抽奖已经开奖，点击查看幸运锦鲤是不是你'
            ],
            'keyword2' => [
                'value' => '如果你是幸运锦鲤，请通过小程序“我的-联系客服”领奖吧'
            ],
            'keyword3' => [
                'value' => '请于开奖后3个工作日内联系客服领奖，逾期作废呢'
            ],
        ];
        foreach ($record as $item)
        {
            $data = $this->send($item['user']['openid'],$item['form_id']);
            if($data['errcode'] != 0)
            {
                throw new AppException($data['errmsg']);
            }
        }
    }
}