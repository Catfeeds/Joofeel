<?php
/**
 * Created by PhpStorm.
 * UserModel: locust
 * Date: 2018/10/23
 * Time: 14:02
 */

namespace App\Services\Token;

use App\Utils\Common;
use Illuminate\Support\Facades\Cache;

class TokenService
{

    public static function generateToken()
    {
        // 32个字符组成一组随机字符串
        $randChars =  Common::getRandChar(32);
        //用三组字符串，进行md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('secure.token_salt');
        return md5($randChars . $timestamp . $salt);
    }
}