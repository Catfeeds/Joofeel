<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/29
 * Time: 9:02
 */

namespace App\Services\MiniProgram\Message\base;

use App\Models\MiniProgram\FormId;
use EasyWeChat\Factory;

class BaseMessage
{
    protected $data;
    protected $tplId;
    protected $page;
    private $app;

    public function __construct()
    {
        $config = config('wechat.mini_program.default');
        $this->app = Factory::miniProgram($config);
    }

    /**
     * @param $openId
     * @param $formId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * 发送
     */
    protected function send($openId,$formId)
    {
        $data = $this->app->template_message->send([
            'touser' => $openId,
            'template_id' => $this->tplId,
            'page' => $this->page,
            'form_id' => $formId,
            'data' => $this->data
        ]);
        return $data;
    }

    /**
     * @param $data
     * @throws AppException
     *准备数据发送模板消息
     */
    protected function getSingleFormId($data)
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
}