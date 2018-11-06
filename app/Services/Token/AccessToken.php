<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/23
 * Time: 17:20
 */

namespace App\Services\Token;

use App\Utils\Curl;

class AccessToken
{
    private $tokenUrl;

    public function __construct(){
        $url = config('wx.access_token_url');
        $url = sprintf($url, config('wx.app_id'), config('wx.app_secret'));
        $this->tokenUrl = $url;
    }

    public function getAccessToken(){
        $token = Curl::curl_get($this->tokenUrl);
        $token = json_decode($token, true);
        if (!$token)
        {
            throw new \Exception('获取AccessToken异常');
        }
        if (!empty($token['errcode']))
        {
            throw new \Exception($token['errmsg']);
        }
        return $token['access_token'];
    }
}