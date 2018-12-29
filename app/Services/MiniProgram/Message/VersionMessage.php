<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/29
 * Time: 9:02
 */

namespace App\Services\MiniProgram\Message;


use App\Services\MiniProgram\Message\base\BaseMessage;
use App\Services\MiniProgram\Message\base\MessageUser;

class VersionMessage extends BaseMessage
{
    public function sendVersion($product,$time,$detail)
    {
        $this->tplId = 'R8ySd9_a2A7ybS7DazUTLatQlJhH-VVE75UURbPpLSs';
        $this->page  = '/pages/donghuatest/donghuatest';
        $this->data  =
            [
                'keyword1' => [
                    'value' => $product
                ],
                'keyword2' => [
                    'value' => $time
                ],
                'keyword3' => [
                    'value' => $detail
                ],
            ];
        $data = (new MessageUser())->getFormId();
        $this->getSingleFormId($data);
    }
}