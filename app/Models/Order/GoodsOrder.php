<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 17:51
 */

namespace App\Models\Order;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class GoodsOrder extends Model
{
    //用户未删除订单
    const NOT_DELETE = 0;
    const DELETE = 1;

    // 待支付
    const UNPAID = 0;
    // 已支付
    const PAID = 1;
    //过期
    const CANCEL = 2;
    //订单已退款
    const REFUND = 3;

    //未发货
    const NOTDELIVERY = 0;
    //已发货
    const DELIVERIED = 1;
    //订单已完成
    const DONE = 2;

    const NOTTRACKINGID = 0;//未填写快递单号


    protected $table = 'goods_order';

    public $timestamps = false;
    protected $fillable =
        [
            'id',
            'user_id',
            'order_id',
            'tracking_id',
            'tracking_company',
            'prepay_id',
            'price',
            'sale_price',
            'sale',
            'carriage',
            'receipt_name',
            'receipt_address',
            'receipt_phone',
            'isSign',
            'isDeleteUser',
            'isPay',
            'created_at',
            'updated_at'
        ];

    public function goods()
    {
        return $this->hasMany(OrderId::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}