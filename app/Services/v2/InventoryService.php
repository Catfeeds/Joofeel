<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/18
 * Time: 14:54
 */

namespace App\Services\v2;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Outbound;
use Illuminate\Http\Request;

define('DAY_TIMESTAMP',86400);

class InventoryService extends Controller
{
    public function add()
    {

    }

    public function get($limit)
    {
        $data = Inventory::query()
                         ->paginate($limit);
        return $this->getInventoryParameter($data);
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索
     */
    public function search($content,$limit)
    {
        $data = Inventory::query()
                         ->where('brand','like','%'.$content.'%')
                         ->orWhere('goods_name','like','%'.$content.'%')
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

    /**
     * @param $id
     * @param $count
     * 出库
     */
    public function update($id,$count)
    {
        $record = Inventory::get($id);
        $record['in_count'] -= $count;
        $price = bcmul ($count,$record['purchase_price'],2);
        $record['in_price'] = bcsub($record['in_price'],$price,2);
        $record->save();
        $this->addOutbound($id,$count,$price);
    }

    /**
     * @param $id
     * @param $count
     * @param $price
     * 添加出库记录
     */
    private function addOutbound($id,$count,$price)
    {
        $admin = Admin::getAdminByToken($this->request->input('token'));
        Outbound::create([
            'inventory_id' => $id,
            'count' => $count,
            'out_price' => $price,
            'executor' => $admin['nickname'],
            'out_date' => date('Y-m-d H:i:s')
        ]);
    }
}