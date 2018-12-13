<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 19:21
 */

namespace App\Services;

use App\Models\Party\Party;

class PartyService
{
    public function search($content,$limit)
    {
        $data = $this->query()->where('u.nickname','like','%'.$content.'%')
                              ->orWhere('party.description','like','%'.$content.'%')
                              ->paginate($limit);
        return $this->htmlEntityDecode($data);
    }

    /**
     * @param $limit
     * @return mixed
     * 获取
     */
    public function get($limit)
    {
        $data = $this->query()
                     ->paginate($limit);
        return $this->htmlEntityDecode($data);
    }

    /**
     * @param $data
     * @return mixed
     * 字符转义以及时间戳
     */
    private function htmlEntityDecode($data)
    {
        foreach ($data as $item)
        {
            $item['site']        = html_entity_decode(base64_decode($item['site']));
            $item['description'] = html_entity_decode(base64_decode($item['description']));
            $item['start_time']  = date('Y-m-d H:i',$item['start_time']);
        }
        return $data;
    }

    /**
     * @return mixed
     * 查询语句
     */
    private function query()
    {
        $query = $data = Party::leftJoin('user as u','u.id','=','party.id')
                              ->select('u.avatar','u.nickname','u.id as user_id',
                                        'party.id as party_id','party.image','party.description',
                                        'party.people_no','party.remaining_people_no','party.start_time',
                                        'party.site');
        return $query;
    }
}