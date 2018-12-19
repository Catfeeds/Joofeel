<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/13
 * Time: 9:12
 */

namespace App\Http\Controllers\Api\v1;


use App\Exceptions\AppException;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    private $service;
    public function __construct(Request $req,UserService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取用户
     */
    public function get()
    {
        $data = $this->service->get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 指定发送优惠券
     */
    public function sendCoupon()
    {
        $this->validate($this->request,
            [
                'user_id'   => 'required|integer|exists:mysql.user,id',
                'coupon_id' => 'required|integer|exists:mysql.coupon,id'
            ]);
        try{
            $this->service->sendCoupon(
                $this->request->input('user_id'),
                $this->request->input('coupon_id'));
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取用户当前可以领取的优惠券
     */
    public function getUserCoupon()
    {
        $this->validate($this->request,
            [
                'user_id'   => 'required|integer|exists:mysql.user,id',
            ]);
        $data = $this->service->getUserCoupon($this->request->input('user_id'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索
     */
    public function search()
    {
        $this->validate($this->request,
            [
                'content' => 'required|string'
            ]);
        $data = $this->service->search(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}