<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/5
 * Time: 22:48
 */

namespace App\Services;

use App\Services\Token\TokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Guard;
use App\Exceptions\AppException;
use App\Models\Admin;

class AdminService
{
    private $auth;

    public function __construct(
        Guard $auth
    ) {
        $this->auth = $auth;
    }

    /**
     * @param string $account 登录帐号
     * @param  string $password 登陆密码
     * @return bool
     * @throws AppException
     * 登录
     */
    public function login($account,$password)
    {
        $admin = Admin::where('account',$account)
                      ->first();
        if($admin)
        {
            if($admin['password'] == md5($password))
            {
                $data['access_token'] = TokenService::generateToken();
                Auth::login($admin,true);
                $data['user'] = Auth::user();
                return $data;
            }
            throw new AppException('账号或密码错误');
        }
        throw new AppException('没有该账号');
    }

    /**
     *退出登录
     */
    public function logout()
    {
        Auth::logout();
    }

    /**
     * @param $id
     * @param $oldPwd
     * @param $newPwd
     * @throws \Exception
     * 修改密码
     */
    public function updateAdminPassword($id,$oldPwd,$newPwd)
    {
        $adminInfoArr = $this->auth->user()->toArray();
        if (! Auth::attempt(['account'=>$adminInfoArr['account'], 'password' =>$oldPwd])) {
            throw new \Exception('旧密码不正确！', 10000);
        }
        $userPassword = md5($newPwd);
        Admin::where('id', $id)->update(['password' => $userPassword]);

    }
}