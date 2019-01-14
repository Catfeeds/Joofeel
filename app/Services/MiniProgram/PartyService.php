<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 19:21
 */

namespace App\Services\MiniProgram;

use App\Models\MiniProgram\Party\Party;
use App\Models\MiniProgram\Party\PartyGoods;
use App\Models\MiniProgram\Party\PartyLabel;
use App\Models\MiniProgram\Party\PartyOrder;
use App\Models\MiniProgram\Party\Message;
use App\Utils\Map;
use App\Utils\WxaCodeUtil;

define('COMMUNITY',-1);
define('NOT_COMMUNITY',0);
define('OFFICIAL',8);

class PartyService
{
    public function addCommunity
    ($description,$details,$way,$people_no,$start_time,$end_time,$sign_time,$city,$site,$image,$contact)
    {
        $location = Map::getLngLat($site);
        $data = [
                'way'                 => $way,
                'site'                => base64_encode($site),
                'user_id'             => OFFICIAL,
                'image'               => $image,
                'people_no'           => $people_no,
                'start_time'          => strtotime($start_time),
                'details'             => base64_encode($details),
                'description'         => base64_encode($description),
                'longitude'           => $location['result']['location']['lng'],
                'latitude'            => $location['result']['location']['lat'],
                'end_time'            => strtotime($end_time),
                'isCommunity'         => Party::COMMUNITY,
                'isDeleteUser'        => Party::NOT_DELETE,
                'sign_time'           => strtotime($sign_time),
                'city'                => $city,
                'remaining_people_no' => $people_no - 1,
                'contact'             => $contact,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ];
        $id =  Party::insertGetId($data);
        $wx_code = (new WxaCodeUtil())->generateWXACode($id);
        Party::where('id',$id)->update(
            [
                'wxa_code' => $wx_code
            ]);
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索聚会
     */
    public function search($content,$limit)
    {
        $data = $this->searchQuery($content)->where('isCommunity',Party::NOT_COMMUNITY)
                                            ->paginate($limit);
        return $this->getJoinCount($data);
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索社区聚会
     */
    public function searchCommunity($content,$limit)
    {
        $data = $this->searchQuery($content)->where('isCommunity',Party::COMMUNITY)
                                            ->paginate($limit);
        return $this->getJoinCount($data);
    }

    public function searchQuery($content)
    {
        $query = $this->query()->where('u.nickname','like','%'.$content.'%')
                               ->orWhere('party.description','like','%'.$content.'%');
        return $query;
    }


    public function getCommunity($limit)
    {
        $data = $this->query()
                     ->where('party.isCommunity',Party::COMMUNITY)
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
        if($sign == NOT_COMMUNITY)
        {
            $data = $this->query()
                         ->where('party.isCommunity',Party::NOT_COMMUNITY)
                         ->paginate($limit);
        }
        else
        {
            $data = $this->query()
                         ->where('u.id',$sign)
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
        $data['data']    = $this->getState($info);
        $data['message'] = $this->getMessage($id);
        $data['join']    = $this->getJoin($id);
        $data['goods']   = $this->getGoods($id);
        $data['label']   = $this->getLabel($id);
        return $data;
    }

    private function getLabel($id)
    {
        $data = PartyLabel::where('party_id',$id)->get();
        return $data;
    }

    private function getGoods($id)
    {
        $data = PartyGoods::leftJoin('goods as g','g.id','=','party_goods.goods_id')
                          ->where('party_goods.party_id','=',$id)
                          ->select('g.thu_url','g.name','g.sale_price')
                          ->get();
        return $data;
    }

    /**
     * @param $id
     * 删除聚会评论
     */
    public function deleteMessage($id)
    {
        Message::where('id',$id)->delete();
    }


    /**
     * @param $id
     * @param $label
     * 给聚会设置标签
     */
    public function label($id,$label)
    {
        PartyLabel::create(
            [
               'party_id'   => $id,
               'label_name' => $label
            ]);
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
                       ->select('message.content','message.created_at','u.nickname','u.avatar','message.id',
                                'message.user_id as message_user','p.user_id as host_user','p.id as party_id',
                                'message.type')
                       ->get();
        foreach ($data as $item)
        {
            $item['content'] = html_entity_decode(base64_decode($item['content']));
            $item['identity'] = $this->getIdentity($item);
        }
        return $data;
    }

    /**
     * @param $data
     * @return string
     * 得到留言者的身份
     */
    private function getIdentity($data)
    {
        if($data['host_user'] == $data['message_user'])
        {
            return Message::HOST;
        }
        else
        {
            $record = PartyOrder::where('party_id',$data['party_id'])
                                ->where('user_id',$data['message_user'])
                                ->select('id')
                                ->first();
            if($record)
            {
                return Message::JOIN;
            }
            return Message::PASSERS;
        }
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
                              ->select('u.avatar','u.nickname','u.id as user_id','party.isClose','party.contact',
                                        'party.id as party_id','party.image','party.description','party.isCommunity',
                                       'party.start_time', 'party.site','party.image','party.way','party.details')
                              ->where('party.isDeleteUser','!=',Party::NOT_HOST)
                              ->orderByDesc('party.created_at');
        return $query;
    }
}