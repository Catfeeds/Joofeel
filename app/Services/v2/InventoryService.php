<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 14:54
 */

namespace App\Services\v2;

use App\Models\Inventory\Inventory;

define('DAY_TIMESTAMP',86400);

class InventoryService
{
    public function add()
    {

    }

    public function get($limit)
    {
        $data = Inventory::orderByDesc('in_day')
                         ->paginate($limit);
        return $this->getInventoryParameter($data);
    }

    /**
     * @param $data
     * @return mixed
     * 得到进销存详细数据
     */
    private function getInventoryParameter($data)
    {
        foreach ($data as $item)
        {
            $item['sold_day'] = floor((strtotime($item['overdue_day']) - time()) / DAY_TIMESTAMP);
            $item['per_sold'] = $this->getPerSold($item);
            $item['can_sold_day'] = floor($item['in_count'] / $item['per_sold']);
        }
        return $data;
    }

    /**
     * @param $item
     * @return float
     * 获取日均销量
     */
    private function getPerSold($item)
    {
        $soldDay =  floor((time() - strtotime($item['in_day'])) / DAY_TIMESTAMP);
        $per_sold = ($item['put_count'] - $item['in_count']) / $soldDay;
        return  round($per_sold, 2);
    }

    public function update()
    {

    }
}