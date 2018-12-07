<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/7
 * Time: 13:19
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class Coupon extends Controller
{

    private $service;

    public function __construct(Request $req,CouponService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }


    public function add()
    {

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有优惠券
     */
    public function all()
    {
        $data = $this->service->all();
        return ResponseUtil::toJson($data);
    }
}