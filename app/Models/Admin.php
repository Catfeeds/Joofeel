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
}