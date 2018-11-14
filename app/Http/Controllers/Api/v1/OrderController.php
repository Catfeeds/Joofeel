<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/14
 * Time: 12:05
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Services\OrderService;
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
                'sign' => 'required|in:0,1,2'
            ]);
        $data = $this->order->get(
            $this->request->input('sign'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}