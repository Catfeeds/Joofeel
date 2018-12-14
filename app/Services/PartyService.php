<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 19:21
 */

namespace App\Services;

use App\Models\Party\Party;
use App\Models\Party\PartyOrder;
use App\Models\Party\Message;


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
     * @param $sign
     * @return mixed
     * 获取聚会
     */
    public function get($limit,$sign)
    {
        if($sign == 0)
        {
            $data = $this->query()
                         ->paginate($limit);
        }
        else
        {
            $data = $this->query()->where('u.id',$sign)
                                  ->paginate($limit);
        }
        return $this->getState($data);
    }

    public function detail($id)
    {

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
        return $this->getJoinCount($data);
    }

    /**
     * @param $data
     * @return mixed
     * 得到参与人数
     */
    private function getJoinCount($data)
    {
        foreach ($data as $item)
        {
            $item['join_count'] = PartyOrder::where('party_id',$item['party_id'])
                                            ->count();
        }
        return $this->getMessageCount($data);
    }

    /**
     * @param $data
     * @return mixed
     * 得到留言个数
     */
    private function getMessageCount($data)
    {
        foreach ($data as $item)
        {
            $item['message_count'] = Message::where('party_id',$item['party_id'])
                                            ->count();
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 获取聚会状态
     */
    public function getState($data)
    {
        foreach ($data as $item)
        {
            switch ($item['isClose'])
            {
                case Party::CLOSE:
                    $item['state'] = Party::STATUS_CLOSE;
                break;
                case Party::DONE:
                    $item['state'] = Party::STATUS_DONE;
                break;
                case Party::NOT_CLOSE:
                    if($item['start_time'] < time())
                    {
                        $item['state'] = Party::STATUS_OVERDUE;
                    }
                    else
                    {
                        $item['state'] = Party::STATUS_DOING;
                    }
            }
        }
        return $this->htmlEntityDecode($data);
    }

    /**
     * @return mixed
     * 查询语句
     */
    private function query()
    {
        $query = $data = Party::leftJoin('user as u','u.id','=','party.user_id')
                              ->select('u.avatar','u.nickname','u.id as user_id','party.isClose',
                                        'party.id as party_id','party.image','party.description',
                                       'party.start_time', 'party.site','party.image','party.way')
                              ->where('party.isDeleteUser','!=',Party::NOT_HOST)
                              ->orderByDesc('party.start_time');
        return $query;
    }
}