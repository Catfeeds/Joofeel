<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/21
 * Time: 9:19
 */

namespace App\Services\Enter;


use App\Models\MiniProgram\User\User;

class UserService
{
    static function getUser($data)
    {
        $user = User::getUser($data['user_id']);
        $data['avatar'] = $user['avatar'];
        $data['nickname'] = $user['nickname'];
        return $data;
    }
}