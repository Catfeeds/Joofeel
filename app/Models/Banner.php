<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:46
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const SHOW = 0;

    const NOT_SHOW = 1;

    protected $table = 'banner';
}