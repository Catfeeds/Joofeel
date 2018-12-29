<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 13:52
 */

namespace App\Http\Controllers\Api\v1\Config;

use App\Http\Controllers\BaseController;
use App\Services\MiniProgram\Message\NotifyMessage;
use App\Services\MiniProgram\Message\VersionMessage;
use App\Utils\ResponseUtil;

class MessageController extends BaseController
{


    /**
     *群发消息
     */
    public function sendNotify()
    {
        $this->validate($this->request,
            [
                'theme' => 'required|string',
                'tips' => 'required|string',
                'note' => 'required|string',
            ]);
        (new NotifyMessage())->sendNotify(
            $this->request->input('theme'),
            $this->request->input('tips'),
            $this->request->input('note'));
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 版本更新通知
     */
    public function sendVersion()
    {
        $this->validate($this->request,
            [
                'product' => 'required|string',
                'time'    => 'required|string',
                'detail'  => 'required|string',
            ]);
        (new VersionMessage())->sendVersion(
            $this->request->input('product'),
            $this->request->input('time'),
            $this->request->input('detail'));
        return ResponseUtil::toJson();
    }
}