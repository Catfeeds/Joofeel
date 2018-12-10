<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 11:48
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Utils\ResponseUtil;

class BannerController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有轮播图
     */
    public function all()
    {
        $data = Banner::orderBy('isShow','asc')
                      ->paginate($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    public function add()
    {

    }
}