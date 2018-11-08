<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/5
 * Time: 22:48
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Admin;
use App\Services\Token\UserToken;

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
        $admin = $this->getAdminByAccount($account);
        if($admin)
        {
            if($admin['password'] == md5($password))
            {
                $admin['login_time'] = time();
                $admin->save();
                $data['access_token'] = $admin['api_token'];
                return $data;
            }
            throw new AppException('账号或密码错误');
        }
        throw new AppException('没有该账号');
    }

    /**
     * @param $account
     * @param $password
     * @param $nickname
     * @param $name
     * @throws AppException
     * 注册
     */
    public function reg($name,$account,$password,$nickname)
    {
        $record = $this->getAdminByAccount($account);
        if($record)
        {
            throw new AppException('该账号已被注册');
        }
        Admin::create([
            'name'      => $name,
            'account'   => $account,
            'password'  => md5($password),
            'nickname'  => $nickname,
            'api_token' => UserToken::generateToken()
        ]);
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
        $admin = self::getAdminByToken($token);
        $admin['nickname'] = $nickname;
        $admin->save();
    }

    static function getAdminByToken($token)
    {
        $admin = Admin::where('api_token',$token)
                       ->first();
        return $admin;
    }

    /**
     * @param $account
     * @return mixed
     */
    public function getAdminByAccount($account)
    {
        $admin = Admin::where('account',$account)
            ->first();
        return $admin;
    }
}