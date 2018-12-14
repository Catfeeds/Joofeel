<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/7
 * Time: 13:19
 */

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    private $service;

    public function __construct(Request $req,CouponService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }


    public function add()
    {
        $this->validate($this->request,
            [
                'category' => 'required|integer|in:0,1,2,3,4',
                'species'  => 'required|integer|in:0,1',
                'rule'     => 'required|integer',
                'sale'     => 'required|integer|min:0',
                'isPoint'  => 'required|integer|in:0,1',
                'start_time' => 'required',
                'end_time' => 'required',
                'day'      => 'required|integer|min:0',
                'count'    => 'required|integer|min:0',
            ]);
        $this->service->add($this->request->all());
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有优惠券
     */
    public function all()
    {
        $data = $this->service->all($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * 下架优惠券
     */
    public function operate()
    {
        $this->validate($this->request,
            [

                'id' => 'required|integer|exists:mysql.coupon,id'
            ]);
        $this->service->operate($this->request->input('id'));
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 发送优惠券
     */
    public function send()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.coupon,id'
            ]);
        try{
            $this->service->send($this->request->input('id'));
        }
        catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 修改数量
     */
    public function update()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.coupon,id',
                'count' => 'required|integer|min:0'
            ]);
        $this->service->update(
            $this->request->input('id'),
            $this->request->input('count'));
        return ResponseUtil::toJson();
    }
}