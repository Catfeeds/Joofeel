<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/19
 * Time: 13:31
 */

namespace App\Http\Controllers;


use App\Models\Admin;
use App\Utils\ResponseUtil;

class BaseController extends Controller
{
    public function checkScope($scope)
    {
        $token = $this->request->input('token');
        $admin = Admin::getAdminByToken($token);
        if($admin['scope'] < $scope)
        {
            return ResponseUtil::toJson('','你没有权利执行此操作',333);
        }
    }
}