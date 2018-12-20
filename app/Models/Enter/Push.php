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


    /**
     * @param $id
     * @param $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 获取商家的推送
     */
    static function getMerchantsPush($id,$limit)
    {
        $data = self::index()
                    ->where('push.merchants_id',$id)
                    ->paginate($limit);
        return $data;
    }

    /**
     * @param $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 获取平台的推送
     */
    static function getPush($limit)
    {
        $data = self::pushQuery()
                    ->paginate($limit);
        return $data;
    }

    static function getSearch($limit,$content)
    {
        $data = self::pushQuery()->where('push.title','like','%'.$content.'%')
                    ->orWhere('m.merchants_name','like','%'.$content.'%')
                    ->orWhere('t.ticket_name','like','%'.$content.'%')
                    ->paginate($limit);
        return $data;
    }

    /**
     * @return mixed
     * 推送页查询
     */
    static function pushQuery()
    {
        $query = self::index()->leftJoin('merchants as m','m.id','=','push.merchants_id')
                     ->select('t.ticket_name','t.thu_url as ticketImage',
                      'push.title','push.thu_url','push.id','m.phone','m.merchants_name');
        return $query;
    }

    private static function index()
    {
        $query = self::leftJoin('ticket as t','t.id','=','push.ticket_id')
                     ->select('t.ticket_name','t.thu_url as ticketImage',
                         'push.title','push.thu_url','push.id')
                     ->orderByDesc('push.created_at');
        return $query;
    }
}