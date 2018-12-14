<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 14:31
 */

namespace App\Services\Util;

use App\Exceptions\AppException;
use App\Models\FormId;
use App\Models\User\User;
use EasyWeChat\Factory;

const MESSAGE_TPL_ID = 'thVitw3RNsJL8Zp9XJHoyol1qJL7UOBx3_E5EXMfyQg';

class MessageService
{
    private $app;

    public function __construct()
    {
        $config = config('wechat.mini_program.default');
        $this->app = Factory::miniProgram($config);
    }

    public function prepareFormId()
    {
        $formId = $this->getFormId();
        $this->prepareData($formId);
    }

    /**
     * @return array
     * 获取formId
     */
    private function getFormId()
    {
        $formId = User::with(['formId' => function($query){
                            $query->where('isUse',FormId::NOT_USE);
                    }])
                      ->select('openid','id')
                      ->get()
                      ->toArray();
        return $this->organize($formId);
    }

    /**
     * @param $data
     * @return array
     * 过滤数据
     */
    private function organize($data)
    {
        foreach ($data as $key => $item)
        {
            if(count($item['form_id']) == 0)
            {
                unset($data[$key]);
            }
        }
        return array_values($data);
    }

    /**
     * @param $data
     * @throws AppException
     *
     */
    private function prepareData($data)
    {
        foreach ($data as $user)
        {
            foreach ($user['form_id'] as $item)
            {
                FormId::where('id',$item['id'])
                      ->update(
                          [
                              'isUse' => FormId::USED
                          ]);
                $result = $this->send($user['openid'],$item['form_id']);
                if($result['errcode'] == 0)
                {
                    break;
                }
            }
        }
    }

    /**
     * @param $openId
     * @param $formId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * 发送
     */
    private function send($openId,$formId)
    {
        $data = $this->app->template_message->send([
            'touser' => $openId,
            'template_id' => MESSAGE_TPL_ID,
            'page' => '/pages/donghuatest/donghuatest/',
            'form_id' => $formId,
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
        return $data;
    }
}