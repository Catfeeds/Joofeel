<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/13
 * Time: 13:58
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Services\PartyService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    private $service;
    public function __construct(Request $req,PartyService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索聚会
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

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取优惠券
     */
    public function get()
    {
        $data = $this->service->get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}