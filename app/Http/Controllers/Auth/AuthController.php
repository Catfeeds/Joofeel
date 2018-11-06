<?php
/**
 * Created by PhpStorm.
 * Admin: locust
 * Date: 2018/11/5
 * Time: 21:15
 */

namespace App\Http\Controllers\Auth;

use App\Exceptions\AppException;
use App\Exceptions\ExceptionCode;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AdminService;
use App\Utils\ResponseUtil;
use Illuminate\Contracts\Auth\Guard;

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
                'account' => 'required|string',
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
     * @param Guard $auth
     * @throws \Exception
     * 退出登录
     */
    public function logout(
        AdminService $service,
        Guard $auth
    )
    {
        if ($auth->guest()) {
            throw new \Exception('退出成功！', ExceptionCode::REDIRECT_TO_LOGIN);
        }
        $service->logout();

        throw new \Exception('退出成功！', ExceptionCode::REDIRECT_TO_LOGIN);
    }

    /**
     * @param AdminService $service
     * @return \Illuminate\Http\JsonResponse
     * 修改密码
     */
    public function updatePwd(AdminService $service)
    {
        $adminId = self::getAdminId();
        $this->validate(
            $this->request,
            [
                'oldPassword' => 'required|string',
                'newPassword' => 'required|string',
            ]
        );
        $service->updateAdminPassword(
            $adminId,
            $this->request->input('oldPassword'),
            $this->request->input('newPassword')
        );
        return ResponseUtil::toJson();
    }

    public function updateInfo()
    {

    }

    /**
     * @return mixed
     * 获取管理员信息
     */
    public function info()
    {
        $id = self::getAdminId();
        $info = Admin::where('id', $id)
                     ->first(
                         [
                             'id',
                             'nickname',
                             'avatar'
                         ]);
        $info['access_token'] = $this->request->input('access_token');
        return ResponseUtil::toJson($info);
    }
}