<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:03
 */

namespace App\Models\Prize;

use App\Models\Goods\Goods;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    const ONGOING = 0; //进行中

    const End = 1;     //已结束

    protected $table = 'prize';

 //   public $timestamps = false;

    protected $fillable =
        [
 //           'id',
            'goods_id',
            'open_prize_time',
  //          'isPrize',
  //          'created_at',
 //           'updated_at'
        ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * 关联商品
     */
    public function goods(){
        return $this->belongsTo(Goods::class,'goods_id','id');
    }

    public function orders(){
        return $this->hasMany(PrizeOrder::class,'prize_id','id');
    }

}