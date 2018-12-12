<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:43
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';

    protected $fillable = [
        'api_token',
        'account',
        'password',
        'nickname',
    ];
    const ALLOW = 0;
    const BANED = 1;

    const PRIMARY = 16;
    const SUPER = 32;
    const BOSS  = 64;
    /**
     * @param $account
     * @return mixed
     * 判断是否注册过
     */
    static function judgeRegistered($account)
    {
        $admin = self::where('account',$account)
                     ->first();
        return $admin;
    }

    static function getAdminById($id)
    {
        $admin = self::where('id',$id)
                     ->first();
        return $admin;
    }

    static function getAdminByToken($token)
    {
        $admin = self::where('api_token',$token)
                     ->first();
        return $admin;
    }

    /**
     * @param $account
     * @return mixed
     */
    static function getAdminByAccount($account)
    {
        $admin = self::where('account',$account)
                     ->first();
        return $admin;
    }
}