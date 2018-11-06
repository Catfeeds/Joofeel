<?php
/**
 * Created by PhpStorm.
 * Admin: locust
 * Date: 2018/11/5
 * Time: 21:15
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Utils\ResponseUtil;

class AuthController extends Controller
{

    /**
     * @param AdminService $service
     * @return \Illuminate\Http\JsonResponse
     * 登录
     */
    public function login(AdminService $service)
    {
        $this->validate(
            $this->request, [
            'account' => 'required|string',
            'password' => 'required|string'
        ]);
        $data = $service->login(
            $this->request->input('account'),
            $this->request->input('password'));
        return ResponseUtil::toJson('1');
    }
}