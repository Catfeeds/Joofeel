<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 11:38
 */

namespace App\Models\Enter;


use Illuminate\Database\Eloquent\Model;

class Push extends Model
{
    protected $connection = 'mysql_enter';

    protected $table = 'push';


    static function getMerchantsPush($id,$limit)
    {
        $data = self::leftJoin('ticket as t','t.id','=','push.ticket_id')
                    ->where('push.merchants_id',$id)
                    ->select('t.ticket_name','t.thu_url as ticketImage','push.title','push.thu_url','push.id')
                    ->paginate($limit);
        return $data;
    }
}