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
            'count',
            'out_price',
            'executor',
            'out_date'
        ];
}