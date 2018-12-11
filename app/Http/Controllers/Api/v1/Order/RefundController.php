<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 16:28
 */

namespace App\Http\Controllers\Api\v1\Order;

use App\Http\Controllers\Controller;
use App\Services\Order\RefundService;
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

    public function get()
    {
        $data = $this->service->get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}