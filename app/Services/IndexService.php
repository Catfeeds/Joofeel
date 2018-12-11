<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/9
 * Time: 13:38
 */

namespace App\Services;

use App\Models\Goods\Goods;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\Order\RefundOrder;
use App\Models\User\User;
use App\Utils\Common;

class IndexService
{
    /**
     * @return array
     * 最近一周新增用户量
     */
    public function recentUser()
    {
        $data = [];
        $date = Common::getWeeks();
        foreach ($date as $key => $item)
        {
            $data[$key]['date'] = date('m-d',strtotime($item));
            $now = date('Y-m-d H:i:s',strtotime($item));
            $next = $this->getNextDate($item);
            $data[$key]['count'] = User::whereBetween('created_at',[$now,$next])
                                       ->count();
        }
        return $data;
    }

    /**
     * @param $date
     * @return false|string
     * 获取下一天的日期
     */
    private function getNextDate($date)
    {
        $nowTime = strtotime($date);
        $nextDayTime = $nowTime + 86400;
        return date('Y-m-d H:i:s',$nextDayTime);
    }

    /**
     *获取销售额分类占比
     */
    public function salePercent()
    {
        $data = OrderId::leftJoin('goods as g','g.id','=','order_id.goods_id')
                       ->where('order_id.isPay',OrderId::PAID)
                       ->select('g.category_id','order_id.price')
                       ->get();
        return $this->categorySale($data);
    }

    /**
     * @param $data
     * @return mixed
     * 获取分类下的营业额
     */
    private function categorySale($data)
    {
        $result['beer'] = $result['wine'] = $result['drinks'] = $result['snacks'] = 0;
        foreach ($data as $item)
        {
            switch ($item['category_id'])
            {
                case Goods::BEER:
                    $result['beer'] = bcadd($result['beer'],$item['price'],1);
                    break;
                case Goods::WINE:
                    $result['wine'] = bcadd($result['wine'],$item['price'],1);
                    break;
                case Goods::DRINKS:
                    $result['drinks'] = bcadd($result['drinks'],$item['price'],1);
                    break;
                case Goods::SNACKS:
                    $result['snacks'] = bcadd($result['snacks'],$item['price'],1);
                    break;
            }
        }
        return $result;
    }

    /**
     * @return mixed
     * 首页待办事项
     */
    public function todo()
    {
        $data['order'] = $this->orderCount();
        $data['goods'] = $this->goodsCount();
        $data['refund'] = $this->refundCount();
        return $data;
    }

    /**
     * 获取未发货订单的个数
     */
    private function orderCount()
    {
        $count = GoodsOrder::where('isSign',GoodsOrder::NOTDELIVERY)
                           ->where('isPay',GoodsOrder::PAID)
                           ->count();
        return $count;
    }

    /**
     * @return mixed
     * 待审商品
     */
    private function goodsCount()
    {
        $count = Goods::where('isPending',Goods::PENDING)
                      ->count();
        return $count;
    }

    /**
     * @return mixed
     * 退款个数
     */
    private function refundCount()
    {
        $count = RefundOrder::where('isAgree',RefundOrder::UNTREATED)
                            ->count();
        return $count;
    }

}