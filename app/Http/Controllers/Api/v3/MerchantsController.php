<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 13:53
 */

namespace App\Http\Controllers\Api\v3;


use App\Http\Controllers\Controller;
use App\Services\Enter\MerchantsService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class MerchantsController extends Controller
{
    private $service;

    public function __construct(Request $req,MerchantsService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取
     */
    public function get()
    {
        $data = $this->service->get($this->request->input('limit'));
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
               'content' => 'required'
            ]);
        $data = $this->service->search(
            $this->request->input('content'),
            $this->request->input('limit')
        );
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 对商铺进行操作
     */
    public function operate()
    {
        $this->validate($this->request,
            [
               'id' => 'required|integer|exists:mysql_enter.merchants,id'
            ]);
        $this->service->operate($this->request->input('id'));
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 商家发布的票
     */
    public function ticket()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql_enter.merchants,id'
            ]);
        $data = $this->service->ticket(
            $this->request->input('id'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 商家发布的票
     */
    public function order()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql_enter.merchants,id'
            ]);
        $data = $this->service->order(
            $this->request->input('id'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 商家发布的票
     */
    public function push()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql_enter.merchants,id'
            ]);
        $data = $this->service->push(
            $this->request->input('id'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}