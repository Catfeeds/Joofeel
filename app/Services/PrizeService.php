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

class PrizeService
{

    /**
     * @param $id
     * @param $time
     * @throws AppException
     * 抽奖
     */
    public function prize($id, $time)
    {
        $result = $this->checkExistPrize();
        if ($result)
        {
            Prize::create([
                'goods_id' => $id,
                'open_prize_time' =>  strtotime($time)
            ]);
        }
        else
        {
            throw new AppException('当前还有商品正在参与抽奖');
        }
    }

    /**
     * @return bool
     * 检查是否还在参与抽奖
     */
    private function checkExistPrize()
    {
        $record = Prize::where('isPrize', Prize::ONGOING)
                       ->where('open_prize_time', '>', time())
                       ->first();
        if ($record)
        {
            return false;
        }
        return true;
    }

    /**
     * @param $limit
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * 抽奖记录
     */
    public function record($limit)
    {
        $data = Prize::leftJoin('goods as g','g.id','=','prize.goods_id')
                     ->select('prize.open_prize_time','prize.isPrize','g.name','g.thu_url','prize.id')
                     ->withCount('orders')
                     ->orderByDesc('prize.created_at')
                     ->paginate($limit);
        foreach ($data as $item)
        {
            $item['open_prize_time'] = date('Y-m-d H:i:s',$item['open_prize_time']);
            if($item['isPrize'] == Prize::End)
            {
                $record = PrizeOrder::with(['user' => function($query){
                                        $query->select('avatar','id','openid');
                                     }])
                                    ->where('prize_id',$item['id'])
                                    ->where('isLucky',PrizeOrder::LUCKY)
                                    ->first();
                $item['user'] = $record['user'];
            }
        }
        return $data;
    }

    /**
     * @param $id
     * @param $userId
     * @throws AppException
     * 开奖(指定用户 随机用户)
     */
    public function open($id,$userId)
    {
        $this->checkOpenValid($id);
        $record = $this->order($id);
        if (count($record) == 0) {
            throw new AppException('没有人参与');
        }
        if ($userId == 0) {
            $userId = $record[rand(0, count($record) - 1)]['user']['id'];

        }
        PrizeOrder::where('prize_id', $id)
            ->where('user_id', $userId)
            ->update([
                'isLucky' => PrizeOrder::LUCKY
            ]);
        (new Message())->sendPrizeMessage($record, $id);
    }

    /**
     * @param $id
     * @throws AppException
     * 检查是否可以开奖
     */
    private function checkOpenValid($id)
    {
        $prize = Prize::where('id',$id)
                      ->select('id','open_prize_time','isPrize')
                      ->first();
        if(time() > $prize['open_prize_time'])
        {
            $prize['isPrize'] = Prize::End;
        }
        else
        {
            throw new AppException('没有到开奖时间');
        }
    }

    /**
     * @param $id
     * @return mixed
     * 详情
     */
    public function info($id)
    {
        $data = Prize::with(['orders' => function($query){
                        $query->with('user')->select('user_id','isLucky','prize_id');
                    }])
                     ->where('id',$id)
                     ->select('id','isPrize')
                     ->first();
        foreach ($data['orders'] as $item)
        {
            $item['isPrize'] = $data['isPrize'];
        }
        return $data['orders'];
    }

    private function order($id)
    {
        $order = PrizeOrder::with(['user' => function ($query) {
                        $query->select('avatar', 'id', 'openid');
                                }])
                            ->where('prize_id', $id)
                            ->get();
        return $order;
    }
}