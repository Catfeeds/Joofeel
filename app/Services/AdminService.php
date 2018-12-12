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

define('UP', 1);
define('DOWN' , 0);

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
        $admin = Admin::getAdminByAccount($account);
        if($admin)
        {
            if($admin['isBaned'] == Admin::BANED)
            {
                throw new AppException('你被禁止登录了');
            }
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
     * @throws AppException
     * 注册
     */
    public function reg($account,$password,$nickname)
    {
        $record = Admin::getAdminByAccount($account);
        if($record)
        {
            throw new AppException('该账号已被注册');
        }
        Admin::create([
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
        $admin = Admin::getAdminByToken($token);
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
        $admin = Admin::getAdminByToken($token);
        $admin['nickname'] = $nickname;
        $admin->save();
    }


    /**
     * @param $id
     * 禁止获取消禁止登录
     */
    public function ban($id)
    {
        $admin = Admin::getAdminById($id);
        if($admin['isBaned'] == Admin::BANED)
        {
            $admin['isBaned'] = Admin::ALLOW;
        }
        else
        {
            $admin['isBaned'] = Admin::BANED;
        }
        $admin->save();
    }

    /**
     * @param $id
     * @param $type
     * @throws AppException
     * 设置权限
     */
    public function set($id,$type)
    {
        //判断是降还是升
        $admin = Admin::getAdminById($id);
        if($type == DOWN)
        {
            if($admin['scope'] == Admin::PRIMARY)
            {
                throw new AppException('已经是最低权限了');
            }
            $admin['scope'] = $admin['scope'] / 2;
        }
        else
        {
            if($admin['scope'] > Admin::BOSS)
            {
                throw new AppException('已经是最高权限了');
            }
            $admin['scope'] = 2 * $admin['scope'];
        }
        $admin->save();
    }
}