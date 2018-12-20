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

    public function get()
    {
        $data = $this->service->get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}