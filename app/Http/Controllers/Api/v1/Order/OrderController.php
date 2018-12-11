<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/14
 * Time: 12:05
 */

namespace App\Http\Controllers\Api\v1\Order;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Services\Order\OrderExcelService;
use App\Services\Order\OrderService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $order;

    public function __construct(Request $req,OrderService $order)
    {
        $this->order = $order;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取订单
     */
    public function get()
    {
        $this->validate($this->request,
            [
                'sign' => 'required|in:0,1,2,3'
            ]);
        $data = $this->order->get(
            $this->request->input('sign'),
            $this->request->input('limit'));

        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索
     */
    public function search()
    {
        $data = $this->order->search(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 发货
     */
    public function delivery()
    {
        $this->validate($this->request,
            [
                'id' => 'required|exists:mysql.goods_order,id'
            ]);
        try
        {
            $this->order->delivery($this->request->input('id'));
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 订单详情
     */
    public function info()
    {
        $this->validate($this->request,
            [
                'id' => 'required|exists:mysql.goods_order,id'
            ]);
        $data = $this->order->info($this->request->input('id'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 单个修改订单信息(快递单号)
     */
    public function update()
    {
        $this->validate($this->request,[
            'id'          => 'required|exists:mysql.goods_order,id',
            'tracking_id' => 'required|string',
            'tracking_company' => 'required|string'
        ]);
        $this->order->update(
            $this->request->input('id'),
            $this->request->input('tracking_id'),
            $this->request->input('tracking_company'));
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 通过Excel更新订单数据
     */
    public function updateExcel()
    {
        (new OrderExcelService())->updateExcel();
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 未发货订单得到Excel数据
     */
    public function orderExcel()
    {
        $this->validate($this->request,
            [
                'sign' => 'required|in:0,1'
            ]);
        (new OrderExcelService())->getOrderExcel($this->request->input('sign'));
    }
}