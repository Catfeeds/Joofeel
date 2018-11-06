<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/5
 * Time: 10:42
 */

namespace App\Models\Order;


use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class RefundOrder extends Model
{

    const UNTREATED = 0; //未处理
    const AGREE = 1;
    const DISAGREE = 2;

    protected $table = 'refund_order';

    protected $fillable = [
        'user_id',
        'order_id',
        'refundNumber',
        'refund_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}