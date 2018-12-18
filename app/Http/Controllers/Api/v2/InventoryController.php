<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 14:29
 */

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Services\v2\InventoryService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    private $service;
    public function __construct(Request $req,InventoryService $service)
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