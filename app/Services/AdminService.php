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
use App\Exceptions\AppException;
use App\Models\Admin;

class AdminService
{



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
                $admin['login_time'] = time();
                $admin->save();
                Auth::login($admin,true);
                $data['access_token'] = TokenService::generateToken();
                return $data;
            }
            throw new AppException('账号或密码错误');
        }
        throw new AppException('没有该账号');
    }

    /**
     * @param $token
     * @param $oldPwd
     * @param $newPwd
     * @return bool
     * @throws \Exception
     * 修改密码
     */
    public function updatePwd($token,$oldPwd,$newPwd)
    {
        $admin = self::getAdmin($token);
        if($admin['password'] == md5($oldPwd))
        {
            $admin['password'] = md5($newPwd);
            $admin->save();
            return true;
        }
        throw new \Exception('旧密码不正确！', 10000);
    }

    /**
     * @param $token
     * @param $nickname
     * 修改信息
     */
    public function updateInfo($token,$nickname)
    {
        $admin = self::getAdmin($token);
        $admin['nickname'] = $nickname;
        $admin->save();
    }
    static function getAdmin($token)
    {
        $admin = Admin::where('api_token',$token)
                       ->first();
        return $admin;
    }
}