<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 17:34
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Prize\Prize;
use App\Models\Prize\PrizeOrder;

class PrizeService extends BaseService
{
    /**
     * @param $id
     * @param $form_id
     * @return bool
     * @throws AppException
     * 参与抽奖
     */
    public function prizeDraw($id,$form_id){
        //找到参与抽奖的记录
        //判断是否存在
        //如果存在判断是否已经参与抽奖
        $prize = Prize::where('id',$id)
                      ->where('isPrize',Prize::ONGOING)
                      ->first();
        if($prize)
        {
            $record = PrizeOrder::where('prize_id',$id)
                                ->where('user_id',$this->uid)
                                ->first();
            if($record)
            {
                throw new AppException('不可重复参与抽奖');
            }
            PrizeOrder::create([
                'prize_id' => $id,
                'user_id'  => $this->uid,
                'form_id'  => $form_id
            ]);
            return true;
        }
        throw new AppException('暂时无奖品参与抽奖');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * @throws AppException
     * 获取正在参与抽奖的奖品信息(从试手气界面进入)
     */
    public function getPrizingInfo(){
        $prize = $this->query()
                      ->where('isPrize',Prize::ONGOING)
                      ->where('open_prize_time','>',time())
                      ->first();
        return $this->checkPrizeExist($prize);
    }

    /**
     * @param $id
     * @return mixed
     * 获取历史抽奖记录(从模板消息进入)
     */
    public function getPrizedInfo($id){
        $prize = $this->query()
                      ->where('id',$id)
                      ->first();
        return $this->checkPrizeExist($prize);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * 公用sql语句
     */
    private function query(){
        $data = Prize::with(['goods'=>function($query){
                     $query->select('id','thu_url','name');
                }])
                     ->select('open_prize_time','id','goods_id')
                     ->withCount('orders');
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * @throws AppException
     * 检查prize是否存在
     */
    private function checkPrizeExist($data){
        if($data)
        {
            return $this->organizePrizeData($data);
        }
        throw new AppException('未找到数据',10000);
    }

    /**
     * @param $data
     * @return mixed
     * 重新整理数据
     */
    private function organizePrizeData($data){
        $order = PrizeOrder::with('user')
                           ->where('prize_id', $data['id'])
                           ->where('user_id', $this->uid)
                           ->first();
        if ($order)
        {
            $data['is_draw'] = true;
        }
        else
        {
            $data['is_draw'] = false;
        }
        $data['avatar'] = $this->getPrizeUserAvatar($order['user']);
        return $data;
    }

    /**
     * @param $user
     * @return mixed
     * 获取虚拟头像
     */
    private function getPrizeUserAvatar($user)
    {
        $data = config('jufeel_config.avatar');
        //将0到19列成一个数组
        //打乱数组
        //截取数组中的某一段得到新数组
        //遍历拿到随机数中的头像
        //返回
        $numbers = range(0, 19);
        shuffle($numbers);
        $result = array_slice($numbers, 0, 16);
        for ($i = 0; $i < 16; $i++) {
            $avatar[$i] = $data[$result[$i]];
        }
        if ($user) {
            $avatar[0] = $user['avatar'];
        }
        return $avatar;
    }
}