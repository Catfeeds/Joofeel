<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 10:00
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\SaleService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    private $service;

    public function __construct(Request $req,SaleService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    public function get()
    {
        $data = $this->service->get();
        return ResponseUtil::toJson($data);
    }
}