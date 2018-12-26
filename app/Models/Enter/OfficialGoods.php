<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 9:09
 */

namespace App\Models\Enter;


use Illuminate\Database\Eloquent\Model;

class OfficialGoods extends Model
{
    protected $table = 'official_goods';
    protected $connection = 'mysql_enter';
    protected $fillable =
        [
            'thu_url',
            'title',
            'price',
            'end_time',
            'url'
        ];
}