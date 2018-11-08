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
        'name'
    ];
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
}