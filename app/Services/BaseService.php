<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 18:01
 */

namespace App\Services;


use App\Services\Token\TokenService;

class BaseService
{
    public $uid;

    public function __construct()
    {
        $this->uid = 1;
    }
}