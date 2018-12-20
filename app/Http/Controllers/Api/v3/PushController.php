<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 16:36
 */

namespace App\Http\Controllers\Api\v3;


use App\Http\Controllers\Controller;
use App\Models\Enter\Push;
use App\Utils\ResponseUtil;

class PushController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有推送
     */
    public function get()
    {
        $data = Push::getPush($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索
     */
    public function search()
    {
        $data = Push::getSearch(
            $this->request->input('limit'),
            $this->request->input('content'));
        return ResponseUtil::toJson($data);
    }
}