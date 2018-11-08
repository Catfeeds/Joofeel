<?php
/**
 * Created by PhpStorm.
 * Admin: locust
 * Date: 2018/11/5
 * Time: 21:15
 */

namespace App\Http\Controllers\Auth;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Models\Admin;
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
            $this->request,
            [
                'account'  => 'required|string',
                'password' => 'required|string'
            ]);
        try{
            $data = $service->login(
                $this->request->input('account'),
                $this->request->input('password'));
        }
        catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson($data);
    }

    /**
     * @param AdminService $service
     * @return \Illuminate\Http\JsonResponse
     * 注册
     */
    public function reg(AdminService $service)
    {
        $this->validate(
            $this->request,
            [
                'account'  => 'required|string',
                'password' => 'required|string',
                'nickname' => 'required|string',
                'account'  => 'required|string',
            ]);
        try{
            $service->reg(
                $this->request->input('name'),
                $this->request->input('account'),
                $this->request->input('password'),
                $this->request->input('nickname'));
        }
        catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }


    /**
     * @param AdminService $service
     * @return \Illuminate\Http\JsonResponse
     * 修改密码
     */
    public function updatePwd(AdminService $service)
    {
        $this->validate(
            $this->request,
            [
                'oldPassword' => 'required|string',
                'newPassword' => 'required|string',
            ]
        );
        $service->updatePwd(
            $this->request->input('token'),
            $this->request->input('oldPassword'),
            $this->request->input('newPassword')
        );
        return ResponseUtil::toJson();
    }

    /**
     * @param AdminService $service
     * @return \Illuminate\Http\JsonResponse
     * 修改信息
     */
    public function updateInfo(AdminService $service)
    {
        $this->validate(
            $this->request,
            [
                'nickname' => 'required|string',
            ]
        );
        $service->updateInfo(
            $this->request->input('token'),
            $this->request->input('nickname')
        );
        return ResponseUtil::toJson();
    }

    /**
     * @return mixed
     * 获取管理员信息
     */
    public function info()
    {
        $token = $this->request->header('token');
        $admin = Admin::where('api_token',$token)
                      ->first();
        return ResponseUtil::toJson($admin);
    }
}