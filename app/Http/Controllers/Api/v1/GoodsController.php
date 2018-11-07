<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/6
 * Time: 17:28
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class GoodsController extends Controller
{

    private $service;
    public function __construct(Request $req,GoodsService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取推荐商品
     */
    public function recommend()
    {
        $data = $this->request->header('token');
       // $data = $this->service->recommend();
        return ResponseUtil::toJson($data);
    }
}