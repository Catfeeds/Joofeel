<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 14:31
 */

namespace App\Services\MiniProgram\Message;

use App\Services\MiniProgram\Message\base\BaseMessage;
use App\Services\MiniProgram\Message\base\MessageUser;

const MESSAGE_TPL_ID = '4o5sKgIguuTUF-EPAmRE0W0tN6yQ6i9yU5OY1HOX3R0';

class NotifyMessage extends BaseMessage
{
    public function sendNotify($theme,$tips,$note)
    {
        $this->prepareData($theme,$tips,$note);
        $data = (new MessageUser())->getFormId();
        $this->getSingleFormId($data);
    }

    private function prepareData($theme,$tips,$note)
    {
        $this->tplId = MESSAGE_TPL_ID;
        $this->page = '/pages/donghuatest/donghuatest';
        $this->data = [
                'keyword1' => [
                    'value' => $theme
                ],
                'keyword2' => [
                    'value' => $tips
                ],
                'keyword3' => [
                    'value' => $note
                ],
            ];
    }
}