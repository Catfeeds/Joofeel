<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 10:07
 */

namespace App\Services;


use App\Models\Order\GoodsOrder;

class SaleService
{
    public function get()
    {
        for($i=GoodsOrder::TODAY;$i<GoodsOrder::YEAR+1;$i++)
        {
            $data[$i] = $this->getDate($i);
        }

        return $data;

    }

    private function getDate($sign)
    {
        switch ($sign)
        {
            case GoodsOrder::TODAY:
                $start =   mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $end = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
                break;
            case GoodsOrder::WEEK:
                $start = strtotime(date('Y-m-d', strtotime("this week Monday", time())));
                $end= strtotime(date('Y-m-d', strtotime("this week Sunday", time()))) + 24 * 3600 - 1;
                break;
            case GoodsOrder::MONTH:
                $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
                $end= mktime(23,59,59,date('m'),date('t'),date('Y'));
                break;
            case GoodsOrder::YEAR:
                $start = mktime(0, 0, 0, 1, 1, date('Y'));
                $end   = mktime(23, 59, 59, 12, 31, date('Y'));
        }
        $start = date('Y-m-d H:i:s',$start);
        $end = date('Y-m-d H:i:s',$end);
        $data = $this->query()
                     ->whereBetween('created_at',[$start,$end])
                     ->get();
        return $this->getPrice($data);
    }

    /**
     * @param $data
     * @return int|string
     * 获得价格
     */
    private function getPrice($data)
    {
        $totalPrice = 0;
        foreach ($data as $item)
        {
            $totalPrice = bcadd($item['sale_price'],$totalPrice,1);
        }
        return $totalPrice;
    }

    private function query()
    {
        $query = GoodsOrder::where('isPay',GoodsOrder::PAID)
                           ->select('sale_price');
        return $query;
    }
}