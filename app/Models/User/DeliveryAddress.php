<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 15:50
 */

namespace App\Models\User;


use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    const HOME = 1;

    const COMPANY = 2;

    const SCHOOL = 3;

    const IS_DEFAULT = 0;

    const NOT_DEFAULT = 1;

    protected  $table = 'delivery_address';

    public $timestamps = false;
    protected $fillable =
        [
            'user_id',
            'receipt_area',
            'receipt_name',
            'receipt_address',
            'receipt_phone',
            'label',
            'isDefault',
        ];

    /**
     * @param $uid
     * @return mixed
     * 获取默认地址
     */
    static function getDefaultAddress($uid)
    {
        $data = self::where('isDefault',self::IS_DEFAULT)
                    ->where('user_id',$uid)
                    ->first();
        return $data;
    }
}