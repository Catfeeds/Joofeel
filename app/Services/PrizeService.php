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
     * @param $time
     * @throws AppException
     * 抽奖
     */
    public function prize($id, $time)
    {
        $result = $this->checkPrize();
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
    private function checkPrize()
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

}