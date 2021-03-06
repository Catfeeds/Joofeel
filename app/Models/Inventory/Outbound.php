<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 17:37
 */

namespace App\Models\Inventory;


use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $connection = 'mysql_inventory';

    protected $table = 'outbound';

    public $timestamps = false;

    protected $fillable =
        [
            'inventory_id',
            'order_id',
            'count',
            'out_price',
            'executor',
            'out_date'
        ];

    static function query()
    {
        $query = self::leftJoin('inventory as i','i.id','=','outbound.inventory_id')
                     ->select('outbound.count','outbound.out_price','outbound.executor','outbound.order_id',
                         'outbound.out_date','i.brand','i.goods_name','i.in_count','i.in_price')
                     ->orderByDesc('outbound.out_date');
        return $query;
    }
}