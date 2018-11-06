<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 18:01
 */

namespace App\Models\Order;

use App\Models\Goods\Goods;
use App\Models\Goods\GoodsLabel;
use Illuminate\Database\Eloquent\Model;

class OrderId extends Model
{
    //用户是否使用
    const NOT_SELECT = 0;
    const SELECT = 1;

    //订单是否支付
    const PAID = 1;
    const UNPAID = 0;
    const REFUND = 3;//已退款(与GoodsOrder保持一致)

    //用户是否删除
    const NOT_DELETE = 0;
    const DELETE = 1;

    protected $table = 'order_id';

    protected $fillable =
        [
            'order_id',
            'goods_id',
            'user_id',
            'party_id',
            'isPay',
            'isDeleteUser',
            'isSelect',
            'count',
            'price'
        ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
    public function orders()
    {
        return $this->belongsTo(GoodsOrder::class, 'order_id', 'id');
    }


    /**
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * 获取用户购买过的商品
     */
    static function getUserGoods($uid){
        $data = self::with(['goods' => function ($query)
                {
                    $query->select('id','name','thu_url','sale_price')
                          ->with('label');
                }
            ])
            ->where([
                'user_id'      => $uid,
                'isPay'        => self::PAID,
                'isDeleteUser' => self::NOT_DELETE
            ])
            ->orderByDesc('updated_at')
            ->select('id','price','goods_id','count','isSelect')
            ->get();
        return $data;
    }
}