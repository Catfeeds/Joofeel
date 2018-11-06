<?php
/**
 * Created by PhpStorm.
 * UserModel: locust
 * Date: 2018/10/23
 * Time: 14:02
 */

namespace App\Services\Token;

use App\Models\User\User as UserModel;
use App\Utils\Curl;
use Illuminate\Support\Facades\Cache;
use Mockery\Exception;

class UserToken extends TokenService
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),
            $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    public function get()
    {

        $result = Curl::curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openID时异常，微信内部错误',201);
        } else {
            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            } else {
                return $this->grantToken($wxResult);
            }
        }
    }

    private function grantToken($wxResult)
    {
        // 拿到openid
        // 数据库里检查openid是否存在
        // 如果不存在则新添一条记录
        // 生成令牌，缓存数据
        // 把令牌返回到客户端
        // key: 令牌
        // value: wxResult,uid,scope
        $openid = $wxResult['openid'];
        $user = UserModel::getByOpenID($openid);
        if ($user) {
            $uid = $user->id;
        } else {
            $uid = $this->newUser($openid);
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $request = Cache::put($key,$value,120); //120分钟
        if (!$request) {
            throw new \Exception('服务器缓存异常',202);
        }
        return $key;
    }

    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        return $cachedValue;
    }

    private function newUser($openid)
    {
        $user = UserModel::create(
            [
                'openid' => $openid
            ]
        );
        return $user->id;
    }

    private function processLoginError($wxResult)
    {
        throw new \Exception($wxResult['errmsg'],$wxResult['errcode']);
    }
}