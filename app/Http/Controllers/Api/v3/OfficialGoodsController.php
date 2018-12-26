<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 9:11
 */

namespace App\Http\Controllers\Api\v3;


use App\Exceptions\AppException;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Controller;
use App\Utils\ResponseUtil;

class OfficialGoodsController extends Controller
{
    public function add()
    {

    }

    public function get()
    {

    }

    public function upload()
    {
        try{
            $data['src'] =
                (new FileController($this->request))->upload('enterjoofeel','official');
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson($data);
    }
}