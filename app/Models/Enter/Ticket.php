<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 11:13
 */

namespace App\Models\Enter;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $connection = 'mysql_enter';

    protected $table = 'ticket';

    const SOLD = 0;
    const NOT_SOLD = 1;


    static function getMerchantsTicket($id,$limit)
    {
        $data = self::where('merchants_id',$id)->paginate($limit);
        return $data;
    }

    /**
     * @param $limit
     * @return mixed
     * 获取
     */
    static function get($limit)
    {
        $data = self::index()->paginate($limit);
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
        $data = self::index()
                    ->where('m.merchants_name','like','%'.$content.'%')
                    ->orWhere('ticket.ticket_name','like','%'.$content.'%')
                    ->paginate($limit);
        return $data;
    }

    static function index()
    {
        $index = self::leftJoin('merchants as m','m.id','=','ticket.merchants_id')
                     ->select('m.merchants_name','m.phone','ticket.ticket_name','ticket.thu_url',
                         'ticket.address','ticket.price','ticket.stock','ticket.start_time',
                         'ticket.end_time','ticket.created_at')
                     ->orderByDesc('ticket.created_at');
        return $index;
    }

}