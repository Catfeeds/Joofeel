<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2019/1/2
 * Time: 9:40
 */

namespace App\Utils;

use App\Services\MiniProgram\Token\AccessToken;
use App\Services\Token\TokenService;

define('PAGE',"pages/index3/index3");

class WxaCodeUtil
{
    private $wxa_code_url;

    public function __construct()
    {
        $accessToken = (new AccessToken())->getAccessToken();
        $this->wxa_code_url = sprintf(config('wx.wxa_code_url'),$accessToken);
    }

    /**
     * @param $id
     * @return mixed
     * 生成小程序二维码
     */
    public function generateWXACode($id)
    {
        $data = array(
            'scene' => $id,
            'page'  => PAGE,
            'width' => 100
        );
        $result = Curl::curl_post($this->wxa_code_url,$data);
        $name = $this->filename();
        file_put_contents("uploads/" . $name, $result);
        return (new ImgUtil())->ossUpload($name,'jufeeloss','test');

    }

    protected function filename()
    {
        $filename = md5(TokenService::generateToken() . time());
        return $filename . '.png';
    }
}