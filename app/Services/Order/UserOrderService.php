<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 18:27
 */

namespace App\Services\Order;

use App\Exceptions\AppException;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Services\BaseService;

class UserOrderService extends BaseService
{
    /**
     * @param $data
     * @throws AppException
     * 软删除来点feel商品
     */
    public function deleteUserGoods($data){
        foreach ($data as $item){
            $result = OrderId::where('id',$item)
                             ->where('user_id',$this->uid)
                             ->update([
                                 'isDeleteUser' => OrderId::DELETE
                             ]);
            if(!$result)
            {
                throw new AppException('删除失败');
            }
        }
    }

    /**
     * @param $id
     * @throws AppException
     * 用户删除订单
     */
    public function deleteUserOrder($id){
        $result = $this->query($id)
                       ->update([
                           'isDeleteUser' => GoodsOrder::DELETE
                       ]);
        if(!$result)
        {
            throw new AppException('删除失败');
        }
    }

    /**
     * @param $id
     * @throws AppException
     * 用户取消订单
     */
    public function cancelUserOrder($id){
        $result = $this->query($id)
                       ->update([
                           'isPay' => GoodsOrder::CANCEL
                       ]);
        if(!$result)
        {
            throw new AppException('操作失败');
        }
    }

    /**
     * @param $id
     * @throws AppException
     * 用户确认收货
     */
    public function completeUserOrder($id){
        $order = $this->query($id)
                      ->where('isSign',GoodsOrder::DELIVERIED)
                      ->first();
        if(!$order)
        {
            throw new AppException('该订单不能确认收货,请重试');
        }
        $order['isSign'] = GoodsOrder::DONE;
        $order->save();
    }

    /**
     * @param $id
     * @return mixed
     * 公用查询语句
     */
    private function query($id)
    {
        $query = GoodsOrder::where('id',$id)
                           ->where('user_id',$this->uid);
        return $query;
    }

}