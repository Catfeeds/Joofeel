<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 16:27
 */

namespace App\Services\Order;

use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use EasyWeChat\Factory;
use App\Exceptions\AppException;
use App\Models\Order\RefundOrder;

class RefundService
{
    private $app;
    public function __construct()
    {
        $config = config('wechat.payment.default');
        $this->app = Factory::payment($config);
    }

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

    /**
     * @param $id
     * @return bool
     * @throws AppException
     * 同意退款
     */
    public function agree($id)
    {
        $record = RefundOrder::leftJoin('goods_order as g','g.id','=','refund_order.order_id')
                             ->where('refund_order.id',$id)
                             ->select('refund_order.refundNumber', 'g.prepay_id',
                                 'g.sale_price','g.id as order_id')
                             ->first();
        if($record['isAgree'] != RefundOrder::UNTREATED)
        {
            throw new AppException('该订单已被处理');
        }
        $result = $this->app->refund->byTransactionId(
            $record['prepay_id'],
            $record['refundNumber'],
            $record['sale_price'],
            $record['sale_price']
        );
        if($result['return_code'] === 'SUCCESS')
        {
            $record['isAgree'] = RefundOrder::AGREE;
            $record->save();
            GoodsOrder::where('id', $record['order_id'])
                      ->update([
                          'isPay' => GoodsOrder::REFUND
                      ]);
            OrderId::where('order_id', $record['order_id'])
                    ->update([
                        'isPay' => OrderId::REFUND
                    ]);
            return true;
        }
        throw new AppException('退款失败');
    }

    public function refuse()
    {

    }
}