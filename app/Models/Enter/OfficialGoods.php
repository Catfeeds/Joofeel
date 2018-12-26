<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 9:09
 */

namespace App\Models\Enter;


use Illuminate\Database\Eloquent\Model;

class OfficialGoods extends Model
{
    protected $table = 'official_goods';
    protected $connection = 'mysql_enter';
    protected $fillable =
        [
            'thu_url',
            'title',
            'price',
            'end_time',
            'url'
        ];

    static function get($limit)
    {
        $data = self::orderByDesc('created_at')->paginate($limit);
        return $data;
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索
     */
    static function search($content,$limit)
    {
        $data = self::orderByDesc('created_at')
                    ->where('title','like','%'.$content.'%')
                    ->paginate($limit);
        return $data;
    }
}