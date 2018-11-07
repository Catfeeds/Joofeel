<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:48
 */

namespace App\Models\Goods;


use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    protected $table = 'recommend';

    protected $fillable = [
        'goods_id'
    ];
    public function goods(){
        return $this->belongsTo(Goods::class,'goods_id','id');
    }

}