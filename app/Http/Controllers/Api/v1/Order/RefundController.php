<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 16:28
 */

namespace App\Http\Controllers\Api\v1\Order;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Services\MiniProgram\Order\RefundService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    private $service;
    public function __construct(Request $req,RefundService $service)
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
     * 同意
     */
    public function agree()
    {
        $this->validate($this->request,
            [
               'id' => 'exists:mysql.refund_order,id'
            ]);
        try {
            $data = $this->service->agree($this->request->input('id'));
        }
        catch (AppException $exception) {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 拒绝
     */
    public function refuse()
    {
        $this->validate($this->request,
            [
                'id'            => 'exists:mysql.refund_order,id',
                'refuse_reason' => 'required|string'
            ]);
        $this->service->refuse(
            $this->request->input('id'),
            $this->request->input('refuse_reason'));
        return ResponseUtil::toJson();
    }
}