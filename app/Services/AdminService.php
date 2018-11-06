<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/5
 * Time: 22:48
 */

namespace App\Services;

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
                $data['access_token'] = 's';
                Auth::login($admin,true);
                return $data;
            }
            throw new AppException('账号或密码错误');
        }
        throw new AppException('没有该账号');
    }
}