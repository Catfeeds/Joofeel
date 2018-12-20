<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 14:31
 */

namespace App\Services\MiniProgram\Util;

use App\Exceptions\AppException;
use App\Models\MiniProgram\FormId;
use App\Models\MiniProgram\User\User;
use EasyWeChat\Factory;

const MESSAGE_TPL_ID = '4o5sKgIguuTUF-EPAmRE0W0tN6yQ6i9yU5OY1HOX3R0';

class MessageService
{
    private $app;

    private $theme;
    private $tips;
    private $note;

    public function __construct()
    {
        $config = config('wechat.mini_program.default');
        $this->app = Factory::miniProgram($config);
    }

    public function prepareFormId($theme,$tips,$note)
    {
        $this->theme = $theme;
        $this->note = $note;
        $this->tips = $tips;
        $formId = $this->getFormId();
        $this->prepareData($formId);
    }

    /**
     * @return array
     * 获取FormId
     */
    private function getFormId()
    {
        $formId = User::with('formId')
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
     *准备数据发送模板消息 当
     */
    private function prepareData($data)
    {
        foreach ($data as $user)
        {
            foreach ($user['form_id'] as $item)
            {
                FormId::where('id',$item['id'])
                    ->delete();
                $result = $this->send($user['openid'],$item['form_id']);
                //单个用户发送成功时退出循环执行下一个用户操作
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
            'page' => '/pages/donghuatest/donghuatest',
            'form_id' => $formId,
            'data' => [
                'keyword1' => [
                    'value' => $this->theme
                ],
                'keyword2' => [
                    'value' => $this->tips
                ],
                'keyword3' => [
                    'value' => $this->note
                ],
            ],
        ]);
        return $data;
    }
}