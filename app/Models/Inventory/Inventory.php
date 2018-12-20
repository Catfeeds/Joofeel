<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 14:30
 */

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $connection = 'mysql_inventory';

    protected $table = 'inventory';

    public $timestamps = false;

    protected $fillable = [
        'brand',
        'goods_name',
        'batch_no',
        'purchase_price',
        'in_count',
        'put_count',
        'in_price',
        'put_price',
        'in_day',
        'overdue_day'
    ];

    static function query()
    {
        $query = self::orderByDesc('id');
        return $query;
    }

    static function get($id)
    {
        $record = self::where('id',$id)->first();
        return $record;
    }
}