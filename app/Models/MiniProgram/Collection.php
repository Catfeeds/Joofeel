<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 19:33
 */

namespace App\Models\MiniProgram;


use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collection';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'user_id',
        'type'
    ];

    const GOODS  = 0;
    const TICKET = 1;
}