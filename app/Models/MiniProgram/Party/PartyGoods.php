<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/26
 * Time: 20:25
 */

namespace App\Models\MiniProgram\Party;


use Illuminate\Database\Eloquent\Model;

class PartyGoods extends Model
{
    protected $table = 'party_goods';

    public $timestamps = false;

    const BUY = 0;
    const COLLECTION = 1;

    const GOODS = 0;
    const TICKET = 1;

    public $fillable = [
        'party_id',
        'goods_id'
    ];
}