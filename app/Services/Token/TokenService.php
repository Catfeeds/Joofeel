<?php
/**
 * Created by PhpStorm.
 * UserModel: locust
 * Date: 2018/10/23
 * Time: 14:02
 */

namespace App\Services\Token;

use App\Utils\Common;
use Illuminate\Http\Request;
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

    public static function getCurrentTokenVar($key)
    {

        $token = (new Request())->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new \Exception('Token已过期或无效Token',401);
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new \Exception('尝试获取Token变量并不存在',401);
            }
        }
    }

    /**
     * @throws TokenException
     * 检测是否存在Token
     */
    public static function checkExistToken()
    {
        $token = (new Request())->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new \Exception('Token已过期或无效Token',401);
        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }


    public static function isValidOperate($checkedUID)
    {
        if (!$checkedUID) {
            throw new \Exception('检查UID时必须传入一个被检测的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if ($currentOperateUID == $checkedUID) {
            return true;
        }
        return false;
    }

    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if ($exist) {
            return true;
        } else {
            return false;
        }
    }
}