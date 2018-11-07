<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/6
 * Time: 17:28
 */

namespace App\Http\Controllers\Api\v1\Goods;

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
        $data = $this->service->recommend();
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 商品详情
     */
    public function info()
    {
        $this->validate(
            $this->request,
            [
               'id' => 'required|integer|exists:mysql.goods,id'
            ]);
        $data = $this->service->info($this->request->input('id'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取分类下的所有商品(上架中)
     */
    public function category()
    {
        $this->validate(
            $this->request,
            [
                'category' => 'required|integer|in:1,2,3,4'
            ]);
        $data = $this->service->category($this->request->input('category'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索
     */
    public function search()
    {
        $this->validate(
            $this->request,
            [
                'content' => 'required|string'
            ]);
        $data = $this->service->search($this->request->input('content'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 上(下)架商品
     */
    public function operate()
    {
        $this->validate(
            $this->request,
            [
                'id' => 'required|integer|exists:mysql.goods,id'
            ]);
        $this->service->operate($this->request->input('id'));
        return ResponseUtil::toJson();
    }
}