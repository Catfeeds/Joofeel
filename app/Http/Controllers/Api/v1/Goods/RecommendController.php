<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/7
 * Time: 17:36
 */

namespace App\Http\Controllers\Api\v1\Goods;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class RecommendController extends Controller
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
        $data = $this->service->recommend($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 取消推荐或推荐(修改状态)
     */
    public function operate()
    {
        $this->validate(
            $this->request,
            [
                'id' => 'required|integer|exists:mysql.goods,id'
            ]);
        $this->service->recommendOperate($this->request->input('id'));
        return ResponseUtil::toJson();
    }

    /**
     *
     * 调整顺序
     */
    public function order()
    {
        $this->validate(
            $this->request,
            [
                'order' => 'required|integer|exists:mysql.recommend,order',
                'type'  => 'required|integer|in:0,1'
            ]
        );
        try{
            $this->service->order($this->request->all());
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }
}