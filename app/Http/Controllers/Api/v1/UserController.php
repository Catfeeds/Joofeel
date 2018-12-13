<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/13
 * Time: 9:12
 */

namespace App\Http\Controllers\Api\v1;


use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $service;
    public function __construct(Request $req,UserService $service)
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