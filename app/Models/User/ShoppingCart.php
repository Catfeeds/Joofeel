<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 15:56
 */

namespace App\Models\User;


use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{

    const NOT_SELECT = 0;

    const SELECTED = 1;

    protected $table = 'shopping_cart';

    protected $fillable =
        [
            'user_id',
            'goods_id',
            'count'
        ];
}