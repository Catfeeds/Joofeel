<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:24
 */

namespace App\Models\Prize;


use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class PrizeOrder extends Model
{
    const NOT_LUCKY = 0;
    const LUCKY = 1;

    protected $table = 'prize_order';

    public $timestamps = false;
    protected $fillable =
        [
            'prize_id',
            'user_id',
            'form_id',
            'created_at',
            'updated_at'
        ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}