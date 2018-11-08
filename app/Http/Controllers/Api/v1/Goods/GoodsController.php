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

    /**
     * 库存紧张的商品
     */
    public function oos()
    {
        $data = $this->service->oos();
        return ResponseUtil::toJson($data);
    }

    /**
     * 修改信息
     */
    public function update()
    {
        $this->validate(
            $this->request,
            [
                'id'                 => 'required|integer|exists:mysql.goods,id',
                'name'               => 'required|string',
                'stock'              => 'required|integer|min:0',
                'notice'             => 'required|string',
                'carriage'           => 'required|integer|min:0',
                'recommend_reason'   => 'required|string',
                'channels'           => 'required|string',
                'purchase_address'   => 'required|string',
                'shop'               => 'required|string',
                'delivery_place'     => 'required',
                'logistics_standard' => 'required|integer|min:0',
                'purchase_price'     => 'required|integer|min:0',
                'cost_price'         => 'required|integer|min:0',
                'reference_price'    => 'required|integer|min:0',
                'price'              => 'required|integer|min:0',
                'sale_price'         => 'required|integer|min:0',
                'country'            => 'required|string'
            ]);
        $this->service->update($this->request->all());
        return ResponseUtil::toJson();
    }

    /**
     *失效商品
     */
    public function failure()
    {
        $data = $this->service->failure();
        return ResponseUtil::toJson($data);
    }

    /**
     * Excel表添加商品
     */
    public function excel()
    {

    }
}