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
    static function query()
    {
        $query = self::orderByDesc('in_day');
        return $query;
    }

    static function get($id)
    {
        $record = self::where('id',$id)->first();
        return $record;
    }
}