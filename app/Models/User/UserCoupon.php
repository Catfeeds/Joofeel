<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 15:24
 */

namespace App\Models\User;


use App\Models\Coupon\Coupon;
use App\Utils\Common;
use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    const NOT_USE = 0;     //未使用
    const USED    = 1;     //已使用
    const CAN_USE = 0;     //可以使用
    const CAN_NOT_USE = 1; //不可以使用


    const ALL = 0; //分类属于所有商品

    protected $table = 'user_coupon';


    protected $hidden =
        [
            'user_id',
        ];

    protected $fillable =
        [
            'user_id',
            'coupon_id',
            'start_time',
            'end_time'
        ];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    /**
     * @param $uid
     * @return array|mixed
     * 获取用户所有优惠券
     */
    static function getUserCoupon($uid){
        $result = self::index($uid)
                      ->get();
        $result = Common::getCouponCategory($result);
        return self::getCouponTime($result);
    }

    /**
     * @param $data
     * @return mixed
     * 转换时间
     */
    static function getCouponTime($data){
        foreach ($data as $item){
            $item['start_time'] = date('Y-m-d',$item['start_time']);
            $item['end_time']   = date('Y-m-d',$item['end_time']);
        }
        return $data;
    }

    /**
     * @param $id
     * @param $uid
     * @return mixed
     * 通过id查找优惠券
     */
    static function getCoupon($id,$uid)
    {
        $record = UserCoupon::where('user_id', $uid)
                            ->where('coupon_id', $id)
                            ->first();
        return $record;
    }

    /**
     * @param $uid
     * @return mixed
     * 获取用户当前可以用的购物券
     */
    static function getCurrentCoupon($uid){
        $data = self::index($uid)
                    ->where('user_coupon.status',UserCoupon::NOT_USE)
                    ->where('user_coupon.state',UserCoupon::CAN_USE)
                    ->where('user_coupon.start_time','<',time())
                    ->where('user_coupon.end_time','>',time())
                    ->get();
        return $data;
    }

    /**
     * @param $uid
     * @return $this
     * 通用sql
     */
    public static function index($uid)
    {
        $query = self::query()
                     ->where('user_id',$uid)
                     ->leftJoin('coupon as c','c.id','=','user_coupon.coupon_id')
                     ->select(
                         [
                             'c.name','c.rule','c.sale','c.category',
                             'user_coupon.start_time','user_coupon.end_time',
                             'user_coupon.state','user_coupon.status'
                         ]);
        return $query;
    }


}