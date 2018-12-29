<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/29
 * Time: 9:08
 */

namespace App\Services\MiniProgram\Message\base;

use App\User;

class MessageUser
{
    /**
     * @return array
     * 获取FormId
     */
    public function getFormId()
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

}