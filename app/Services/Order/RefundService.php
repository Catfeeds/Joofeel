<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 16:27
 */

namespace App\Services\Order;


use App\Models\Order\RefundOrder;

class RefundService
{
    public function get($limit)
    {
        $data = RefundOrder::leftJoin('goods_order as o','o.id','=','refund_order.order_id')
                           ->where('refund_order.isAgree',RefundOrder::UNTREATED)
                           ->orderByDesc('refund_order.created_at')
                           ->select('o.receipt_name','o.receipt_address','o.receipt_phone',
                               'o.order_id','o.sale_price','refund_order.id','refund_order.refund_reason')
                           ->paginate($limit);
        return $data;
    }
}