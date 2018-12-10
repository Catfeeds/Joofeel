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
use App\Services\BannerService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    private $service;
    public function __construct(Request $req,BannerService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

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

    /**
     * @return \Illuminate\Http\JsonResponse
     * 上下架banner
     */
    public function operate()
    {
        $this->validate($this->request,
            [

                'id' => 'required|integer|exists:mysql.banner,id'
            ]);
        $this->service->operate($this->request->input('id'));
        return ResponseUtil::toJson();
    }
}