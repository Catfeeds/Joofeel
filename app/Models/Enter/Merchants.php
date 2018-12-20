<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 11:38
 */

namespace App\Models\Enter;

use Illuminate\Database\Eloquent\Model;

class Merchants extends Model
{
    protected $connection = 'mysql_enter';

    protected $table = 'merchants';

    protected $fillable =
        [
            'account',
            'password',
            'api_token',
            'merchants_name',
            'phone'
        ];

    protected $hidden =
        [
            'account',
            'password',
            'api_token'
        ];

    const NOT_BAN = 0;
    const BAN = 1;

    public function push()
    {
        return $this->hasMany(Push::class,'merchants_id','id');
    }

    public function ticket()
    {
        return $this->hasMany(Ticket::class,'merchants_id','id');
    }

    public function order()
    {
        return $this->hasMany(TicketOrder::class,'ticket_id','id');
    }

    static function query()
    {
        $query = self::withCount('push')
                     ->withCount('ticket')
                     ->withCount('order')
                     ->orderByDesc('created_at');
        return $query;
    }
}