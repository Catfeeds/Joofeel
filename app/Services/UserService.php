<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/30
 * Time: 10:06
 */

namespace App\Services;

use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\Party\Party;
use App\Models\Party\PartyOrder;
use App\Models\User\DeliveryAddress;
use App\Models\User\User;
use App\Models\User\UserCoupon;
use App\Utils\Common;

const dayTimeStamp = 86400;

class UserService extends BaseService
{
    /**
     * @return mixed
     * 获取用户举办过的派对
     */
    public function getUserHostParty(){
        $data = Party::getUserHostParty($this->uid);
        return $this->getPartyStatus($data);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     * 获取用户参加过的派对
     */
    public function getUserJoinParty(){
        $data = PartyOrder::getUserJoinParty($this->uid);
        $party = [];
        foreach ($data as $item) {
            array_push($party, $item['party']);
        }
        return $this->getPartyStatus($party);
    }


    /**
     * @return mixed
     * 获取用户的收货地址
     */
    public function getUserDeliveryAddress(){
        $data = DeliveryAddress::where('user_id', $this->uid)
                               ->orderBy('id' ,'desc')
                               ->get()
                               ->toArray();
        $status = [
            'hasDefault' => false,
            'pIndex' => -1
        ];
        //判断是否有默认地址 如果有改变标记值并记录该地址的位置
        foreach ($data as $key => $item) {
            if ($item['isDefault'] == DeliveryAddress::IS_DEFAULT) {
                $status['hasDefault'] = true;
                $status['pIndex'] = $key;
            }
        }
        //有默认地址的情况
        //先将该地址复制到第一个
        //删除
        if ($status['hasDefault'] == true) {
            array_unshift($data, $data[$status['pIndex']]);
            $status['pIndex'] += 1;
            array_splice($data, $status['pIndex'], 1);
        }
        $result = Common::getAddressLabel($data);
        return $result;
    }

    /**
     * @return mixed
     * 获取用户的优惠券
     */
    public function getUserCoupon(){

        //获取用户所有的优惠券
        //根据情况将优惠券划分到相应的数组

        //可以使用:1、购物券未被用户使用;2、购物券未被管理员下架;3、购物券的结束时间大于此时时间
        //不可以使用:1、购物券被用户使用
        //过期:1、购物券未被用户使用;2、购物券未被管理员下架;3、购物券的结束时间小于此时时间

        $result = UserCoupon::getUserCoupon($this->uid);
        $coupon['used'] = $coupon['not_use'] = $coupon['overdue'] = array();
        foreach ($result as $item){
            if(
                $item['status'] == UserCoupon::NOT_USE &&
                $item['state'] == UserCoupon::CAN_USE &&
                $item['end_time'] > date('Y-m-d')
            ) {
                array_push($coupon['not_use'], $item);
            }
            else if(
                $item['status'] == UserCoupon::USED
            ) {
                array_push($coupon['used'], $item);
            }
            else {
                array_push($coupon['overdue'], $item);
            }
        }
        return $coupon;
    }

    /**
     * @return mixed
     * 获取用户购买过的商品
     */
    public function getUserGoods(){

        //将用户购买过的所用商品取出
        //命名两个数组
        //分情况重组数组

        $data = OrderId::getUserGoods($this->uid);
        $goods['not_use'] =  $goods['used'] = array();
        foreach ($data as $item){
            if($item->isSelect == 0){
                array_push($goods['not_use'], $item);
            }
            else{
                array_push($goods['used'], $item);
            }
        }
        return $goods;
    }


    /**
     * @return mixed
     * 获取用户的订单
     */
    public function getUserOrder(){
        //获取用户所有的订单
        //分三种情况将订单分类
        $data = GoodsOrder::with(['goods' => function ($query) {
                                          $query->select('id','order_id','goods_id')
                                                ->with(['goods' => function ($query)
                                                {
                                                    $query->select('id','thu_url');
                                                }]);
                                }])
                          ->where('isDeleteUser',GoodsOrder::NOT_DELETE)
                          ->where('user_id',$this->uid)
                          ->select('id','order_id','price','isPay','created_at')
                          ->orderByDesc('created_at')
                          ->get();
        $order['cancel'] = $order['unfinished'] = $order['done'] = array();
        foreach ($data as $item){
            //计算订单支付过的时间
            //done:完成的订单(已支付)
            //unfinished:未完成的订单(未支付且必须为在24小时内生成的订单)
            //cancel:取消的订单(用户主动取消的订单或者未支付且生成订单的时间超过24小事)
            $create_at = strtotime($item['created_at']);
            $overTime = time() - $create_at;
            if($item['isPay'] == GoodsOrder::PAID){
                array_push($order['done'],$item);
            }
            else if(
                $item['isPay'] == GoodsOrder::UNPAID &&
                $overTime < dayTimeStamp
            ) {
                array_push($order['unfinished'],$item);
            }
            else{
                array_push($order['cancel'],$item);
            }
        }
        return $order;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * 获取订单详情
     */
    public function getUserOrderInfo($id){
        $order = GoodsOrder::with(['goods'=>function($query)
            {
                $query->select('order_id','goods_id','count','price')
                    ->with(['goods'=>function($query){
                    $query->select('thu_url','name','id');
                }]);
            }
        ])
            ->where('id',$id)
            ->first(
                [
                    'id','order_id','receipt_name',
                    'receipt_address','receipt_phone',
                    'price','sale_price','sale','carriage'
                ]);
        return $order;
    }


    /**
     * @param $avatar
     * @param $nickname
     * 上传用户头像昵称
     */
    public function saveUserInfo($avatar,$nickname){
        $data = [
            'avatar'   => $avatar,
            'nickname' => $nickname
        ];
        User::where('id',$this->uid)->update($data);
    }

    /**
     * @param $data
     * @return mixed
     * 获取派对的状态
     */
    private function getPartyStatus($data){
        foreach ($data as $item)
        {
            //判断聚会是否关闭、或者提前成行
            //未关闭的时候还要判断聚会开始时间是否大于此时的时间
            //根据不同情况标记此时聚会状态
            switch ($item['isClose'])
            {
                case Party::NOT_CLOSE:
                    if($item['start_time'] > time())
                    {
                        $item['pStatus'] = '发布中';
                    }
                    else
                    {
                        $item['pStatus'] = '已发布';
                    }
                    break;
                case Party::CLOSE:
                    $item['pStatus'] = '已取消';
                    break;
                case Party::DONE:
                    $item['pStatus'] = '已发布';
                    break;
            }
        }
        return $data;
    }
}