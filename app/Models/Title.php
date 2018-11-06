<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:09
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    const SHOW = 0;

    const NOT_SHOW = 1;

    //聚小仓界面今日推荐标题
    protected $table = 'title';


}