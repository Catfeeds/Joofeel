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
        return $this->getJoinCount($data);
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
        return $this->getJoinCount($data);
    }

    /**
     * @param $id
     * @return mixed
     * 聚会详情
     */
    public function detail($id)
    {
        $info = $this->query()
                     ->where('party.id',$id)
                     ->get();
        $data['data'] = $this->getState($info);
        $data['message'] = $this->getMessage($id);
        $data['join']    = $this->getJoin($id);
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * 获得参与者
     */
    private function getJoin($id)
    {
        $data = PartyOrder::leftJoin('party as p','p.id','=','party_order.party_id')
                          ->leftJoin('user as u','u.id','=','party_order.user_id')
                          ->where('p.id',$id)
                          ->select('party_order.created_at','u.nickname','u.avatar')
                          ->get();
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * 获得留言
     */
    private function getMessage($id)
    {
        $data = Message::leftJoin('party as p','p.id','=','message.party_id')
                       ->leftJoin('user as u','u.id','=','message.user_id')
                       ->where('p.id',$id)
                       ->select('message.content','message.created_at','u.nickname','u.avatar')
                       ->get();
        foreach ($data as $item)
        {
            $item['content'] = html_entity_decode(base64_decode($item['content']));
        }
        return $data;
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
            $item['details']     = html_entity_decode(base64_decode($item['details']));
            $item['start_time']  = date('Y-m-d H:i',$item['start_time']);
        }
        return $data;
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
        return $this->getState($data);
    }

    /**
     * @param $data
     * @return mixed
     * 获取聚会状态
     */
    private function getState($data)
    {
        foreach ($data as $item)
        {
            if($item['isClose'] == Party::CLOSE) {
                $item['state'] = Party::STATUS_CLOSE;
            }
            else if($item['isClose'] == Party::DONE) {
                $item['state'] = Party::STATUS_DONE;
            }
            else {
                if($item['start_time'] < time()) {
                    $item['state'] = Party::STATUS_OVERDUE;
                }
                else{
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
                                       'party.start_time', 'party.site','party.image','party.way','party.details')
                              ->where('party.isDeleteUser','!=',Party::NOT_HOST)
                              ->orderByDesc('party.start_time');
        return $query;
    }
}