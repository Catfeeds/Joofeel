<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:48
 */

namespace App\Models\MiniProgram\Goods;

use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    protected $table = 'recommend';


    public $timestamps = false;
    protected $fillable = [
        'id',
        'order',
        'goods_id'
    ];
    public function goods(){
        return $this->belongsTo(Goods::class,'goods_id','id');
    }

    static function getByOrder($order)
    {
        $record = self::where('order',$order)->first();
        return $record;
    }
}