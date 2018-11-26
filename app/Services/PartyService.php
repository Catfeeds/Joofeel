<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 19:21
 */

namespace App\Services;


use App\Exceptions\AppException;
use App\Models\Order\OrderId;
use App\Models\Party\Message;
use App\Models\Party\Party;
use App\Models\Party\PartyGoods;
use App\Models\Party\PartyOrder;

class PartyService extends BaseService
{
    /**
     * @param $id
     * @return mixed
     * @throws AppException
     * 参加聚会
     */
    public function join($id)
    {
        //不能被管理员删除
        //不能是自己发起的聚会
        //不能被发起者关闭
        //开始时间必须大于此时时间
        //报名剩余人数>0
        //判断是否已经参加过此次聚会
        $party = Party::where('isDeleteAdmin',Party::NOT_DELETE)
                      ->where('user_id','!=',1)
                      ->where('isClose',Party::NOT_CLOSE)
                      ->where('start_time','>',time())
                      ->where('remaining_people_no','>',0)
                      ->where('id',$id)
                      ->select('people_no','remaining_people_no','id')
                      ->first();
        if($party)
        {
            $record = PartyOrder::where('party_id',$id)
                                ->where('user_id',$this->uid)
                                ->first();
            if($record)
            {
                throw new AppException('已经报过名啦!');
            }
            if($party['people_no'] < 11)
            {
                $party['remaining_people_no'] -= 1;
                $data = $party->save();
            }
            PartyOrder::create([
                'user_id' => $this->uid,
                'party_id' => $id
            ]);
            return $data;
        }
        throw new AppException('暂时无法参加,请稍后重试');
    }

    /**
     * @param $id
     * @throws AppException
     * 关闭聚会
     */
    public function close($id)
    {
        $result = $this->query($id)
                       ->update([
                           'isClose' => Party::CLOSE
                       ]);
        if(!$result)
        {
            throw new AppException('操作失败');
        }
    }

    /**
     * @param $id
     * @throws AppException
     * 提前成行
     */
    public function complete($id)
    {
        $result = $this->query($id)
                       ->update([
                           'isClose' => Party::DONE
                       ]);
        if(!$result)
        {
            throw new AppException('操作失败');
        }
    }

    /**
     * @param $id
     * @param $content
     * @return bool
     * @throws AppException
     * 评论
     */
    public function comment($id,$content)
    {
        //没有被管理员删除
        //聚会没有关闭
        //聚会开始时间大于此时时间
        $party = Party::where('isDeleteAdmin',Party::NOT_DELETE)
                      ->where('isClose',Party::NOT_CLOSE)
                      ->where('start_time','>',time())
                      ->where('id',$id)
                      ->first();
        if($party)
        {
            Message::create([
                'content'  => $content,
                'party_id' => $id,
                'user_id'  => $this->uid
            ]);
            return true;
        }
        throw new AppException('不能评论');
    }

    /**
     * @param $description
     * @param $way
     * @param $people_no
     * @param $date
     * @param $time
     * @param $site
     * @param $image
     * @param $orders
     * @param $latitude
     * @param $longitude
     * 举办派对
     */
    public function host(
        $description,
        $way,
        $people_no,
        $date,
        $time,
        $site,
        $image,
        $orders,
        $latitude,
        $longitude
    ){
        //此时聚会并未发起
        //isDeleteUser            = 1 处于删除状态
        $data = [
            'way'   => $way,
            'date'  => $date,
            'time'  => $time,
            'site'  => $site,
            'image' => $image,
            'user_id'     => $this->uid,
            'latitude'    => $latitude,
            'longitude'   => $longitude,
            'people_no'   => $people_no,
            'start_time'  => strtotime($date .$time),
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
            'isDeleteUser'        => Party::CLOSE,
            'remaining_people_no' => $people_no - 1,
        ];
        $id = Party::insertGetId($data);
        foreach ($orders as $item)
        {
            PartyGoods::create([
                'goods_id' => $item['goods_id'],
                'party_id' => $id
            ]);
        }
    }

    /**
     * @param $id
     * 用户点击预览分享后绑定聚会的使用物品以及将聚会开放
     */
    public function bind($id)
    {
        $this->query($id)
             ->update([
                 'isDeleteUser' => Party::NOT_DELETE
             ]);
    }

    /**
     * @param $id
     * @return mixed
     * @throws AppException
     * 举办者软删除聚会
     */
    public function delete($id){
        $result = $this->query($id)
                       ->update([
                           'isDeleteUser' => Party::DELETE
                       ]);
        if(!$result)
        {
            throw new AppException('删除失败请重试',0);
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws AppException
     * 参与者软删除聚会
     */
    public function deleteOrder($id){
        $result = PartyOrder::where('id',$id)
                            ->where('user_id',$this->uid)
                            ->update([
                                'isDeleteUser' => PartyOrder::DELETE
                            ]);
        if(!$result)
        {
            throw new AppException('删除失败请重试',0);
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws AppException
     * 聚会详情
     */
    public function get($id)
    {
        //关联参与者->用户
        //关联商品订单(order_id)->商品(goods)
        //关联留言->用户
        //关联用户(user发起者)
        $data = Party::with(['participants'=>function($query){
            $query->with(['user' => function ($query) {
                $query->select('avatar','id');
            }])
                ->select('user_id','party_id')
                ->orderByDesc('created_at');
        }])
            ->with(['goods' => function ($query) {
                $query->select('id','goods_id','party_id')
                      ->with(['goods' => function ($query) {
                            $query->select('id','name','price','sale_price','thu_url');
                      }]);
            }])
            ->with(['message' => function ($query) {
                $query->with(['user'=>function($query){
                    $query->select('avatar','nickname','id');
                }])
                    ->select('user_id','party_id')
                    ->orderByDesc('created_at');
            }])
            ->with(['user' => function ($query) {
                $query->select('id','avatar','nickname');
            }])
            ->where('isDeleteAdmin', Party::NOT_DELETE)
            ->where('id',$id)
            ->select([
                'id','user_id','image','description',
                'way','people_no','remaining_people_no',
                'date','time','site','longitude','latitude',
                'start_time','isClose'
            ])
            ->first();
        if($data)
        {
            //得到参与者的头像(虚拟)
            $data['avatar'] = $this->organizeAvatar($data['participants']);
            //将participants从数组中删除
            unset($data['participants']);
            return $this->getPartyStatus($data);
        }
        throw new AppException('聚会不存在');

    }

    /**
     * @param $data
     * @return array
     * 重新组装聚会详情页参与者的头像
     */
    public function organizeAvatar($data)
    {
        //判断参与者是否已达6人,
        //如果没有则填充头像

        $array = array();
        if (count($data) < 6)
        {
            for ($i = 0; $i < count($data); $i++)
            {
                $array[$i]['avatar'] = $data[$i]['user']['avatar'];
            }
            for ($i = count($data); $i < 6; $i++)
            {
                $array[$i]['avatar'] =
                    'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/icon/837368762097679221.png';
            }
        }
        else
        {
            for ($i = 0; $i < count($data); $i++)
            {
                $array[$i]['avatar'] = $data[$i]['user']['avatar'];
            }
        }
        return $array;
    }

    /**
     * @param $party
     * @return mixed
     * 获取查看聚会的用户的身份以及聚会此时的状态
     */
    private function getPartyStatus($party)
    {
        //先判定状态
        //除了进行中,其余三中状态对于每种人来说都是一样的视图
        //进行中->判定身份
        //如果是路人还要判定聚会人数是否已满
        //详情参考OSS、聚会状态.xlsx
        if($party['state'] == Party::CLOSE)
        {
            $pStatus = Party::STATUS_CLOSE;
        }
        else if($party['state'] == Party::DONE)
        {
            $pStatus = Party::STATUS_DONE;
        }
        else if($party['state'] == Party::NOT_CLOSE && $party['start_time'] < time())
        {
            $pStatus = Party::STATUS_OVERDUE;
        }
        else
        {
            if($party['user_id'] == $this->uid)
            {
                $pStatus = Party::STATUS_OPEN_HOST;
            }
            else
            {
                $record = PartyOrder::where('party_id', $party['id'])
                                    ->where('user_id', $this->uid)
                                    ->first();
                if($record)
                {
                    $pStatus = Party::STATUS_OPEN_JOIN;
                }
                else
                {
                    if($party['remaining_people_no'] > 0)
                    {
                        $pStatus = Party::STATUS_OPEN_PASSERBY_NOT_FULL;
                    }
                    else
                    {
                        $pStatus = Party::STATUS_OPEN_PASSERBY_FULL;
                    }
                }
            }
        }
        $party['pStatus'] = $pStatus;
        return $this->getMessageIdentity($party);
    }

    /**
     * @param $data
     * @return mixed
     * 获取评论人的身份
     */
    public function getMessageIdentity($data)
    {
        //遍历所有消息
        //遍历所有参与者
        //判断发消息的人user_id是否属于聚会发起者
        //判断user_id是否属于参与者
        foreach ($data['message'] as $d_m)
        {
            if ($d_m['user_id'] == $data['user_id'])
            {
                $d_m['identity'] = '发起者';
            }
            else
            {
                $pStatus = false;
                foreach ($data['participants'] as $d_p)
                {
                    if ($d_m['user_id'] == $d_p['user_id'])
                    {
                        $pStatus = true;
                        $d_m['identity'] = '参与者';
                    }
                }
                if ($pStatus == false)
                {
                    $d_m['identity'] = '路人';
                }
            }
        }
        return $data;
    }

    /**
     * @param $id
     * @return mixed
     * 通过查询语句
     */
    private function query($id)
    {
        $query = Party::where('id',$id)
                      ->where('user_id',$this->uid);
        return $query;
    }

}