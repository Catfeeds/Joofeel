<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/29
 * Time: 16:46
 */

namespace App\Models\MiniProgram;


use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    const SHOW = 0;

    const NOT_SHOW = 1;

    const GOODS_DETAIL = 1;
    const ACTIVITY_DETAIL = 2;
    const GOODS_CATEGORY = 3;

    const PRIZE = 1;
    const NOT_PRIZE = 0;

    protected $table = 'banner';

    protected $fillable =
        [
            'type',
            'url',
            'goods_id',
            'isPrize',
            'isShow',
            'image'
        ];

    static function get($id)
    {
        $banner = self::where('id',$id)->first();
        return $banner;
    }
}