<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 11:39
 */

namespace App\Models\Enter;


use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    protected $connection = 'mysql_enter';

    protected $table = 'ticket_order';

    const NOT_PAY = 0;
    const PAID = 1;
    /**
     * 使用情况
     */
    const NOT_USE = 0;
    const USED = 1;


    static function getMerchantsOrder($id,$limit)
    {
        $data = self::index()
                    ->where('ticket_order.merchants_id',$id)
                    ->paginate($limit);
        return $data;
    }

    static function getOrder($limit)
    {
        $data = self::index()->paginate($limit);
        return $data;
    }

    static function index()
    {
        $query =  self::leftJoin('ticket as t','t.id','=','ticket_order.ticket_id' )
                      ->where('ticket_order.isPay',self::PAID)
                      ->select('ticket_order.order_id','ticket_order.price','ticket_order.count',
                          'ticket_order.total_price','ticket_order.created_at','t.ticket_name',
                          't.thu_url','ticket_order.user_id');
        return $query;

    }
}