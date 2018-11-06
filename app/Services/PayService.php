<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/2
 * Time: 16:33
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Goods\Goods;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\Order\RefundOrder;
use App\Services\Order\OrderService;
use App\Services\Token\TokenService;
use EasyWeChat\Factory;
use function EasyWeChat\Kernel\Support\generate_sign;

class PayService
{
    private $id;      //goods_order主键
    private $orderId; //自定义id
    private $app;

    public function __construct()
    {
        $config = config('wechat.payment.default');
        $this->app = Factory::payment($config);
    }

    public function pay($order_id)
    {
        $this->id = $order_id;
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->id);
        if (!$status['pass']) {
            return $status;
        }
        $order = GoodsOrder::find($this->id);
        return $this->makeWxPreOrder($order['sale_price'] + $order['carriage']);
    }

    /**
     * @param $totalPrice
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws AppException
     */
    public function makeWxPreOrder($totalPrice)
    {

        $openid = TokenService::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new AppException('为找到用户');
        }
        $result = $this->app->order->unify([
            'body'         => 'Jufeeling',
            'out_trade_no' => $this->orderId,
            'trade_type'   => 'JSAPI',     // 必须为JSAPI
            'openid'       => $openid,     // 这里的openid为付款人的openid
            'total_fee'    => $totalPrice, // 总价
            'notify_url'   => 'jufeel.jufeeling.com/pay/notify'
        ]);
        if ($result['return_code'] === 'SUCCESS')
        {
            // 二次签名的参数必须与下面相同
            $params =
                [
                    'appId'     => 'wx35c845c26c3c6c61',
                    'timeStamp' => time(),
                    'nonceStr'  => $result['nonce_str'],
                    'package'   => 'prepay_id=' . $result['prepay_id'],
                    'signType'  => 'MD5',
                ];
            $params['paySign'] = generate_sign($params, config('wechat.payment.default.key'));
            $this->recordPreOrder($result);
            return $params;
        }
        else
        {
            return $result;
        }

    }

    /**
     * @param $wxOrder
     *
     */
    private function recordPreOrder($wxOrder)
    {
        //将prepay_id存进数据库
        GoodsOrder::where('id',$this->id)
                  ->update([
                      'prepay_id' => $wxOrder['prepay_id']
                  ]);
    }

    /**
     * @return bool
     * @throws AppException
     * 检查订单是否符合要求
     */
    private function checkOrderValid()
    {
        //必须是在24小时内生成的订单
        //订单状态必须为未支付
        //检查是否是该用户生成的订单
        $startTime = date('Y-m-d H:i:s',strtotime("-1 day"));
        $endTime   = date('Y-m-d H:i:s');
        $order = GoodsOrder::where('id',$this->id)
                           ->where('isPay',GoodsOrder::UNPAID)
                           ->where('user_id',TokenService::getCurrentUid())
                           ->whereBetween('created_at',[$startTime,$endTime])
                           ->first();
        if($order)
        {
            $this->orderId = $order['order_id'];
            return true;
        }
        throw new AppException('支付错误,请重试');
    }

    /**
     * @param $id
     * 支付失败
     */
    public function fail($id)
    {
        $records = OrderId::where('order_id',$id)
                          ->get();
        foreach ($records as $item)
        {
            $goods = Goods::where('id',$item['goods_id'])
                          ->first();
            $goods['stock'] += $item['count'];
            $goods->save();
        }
    }

    /**
     * @param $id
     * 支付成功
     */
    public function success($id)
    {
        GoodsOrder::where('id',$id)
                  ->update(
                      [
                          'isPay' => GoodsOrder::PAID
                      ]);
        OrderId::where('order_id',$id)
               ->update(
                   [
                       'isPay' => GoodsOrder::PAID
                   ]);
    }

    /**
     * @param $order_id
     * @param $refund_reason
     * @return bool
     * @throws AppException
     * 提交退款申请
     */
    public function submitRefund($order_id,$refund_reason)
    {
        $order = GoodsOrder::where('id',$order_id)
                           ->where('user_id',TokenService::getCurrentUid())
                           ->where('isPay',GoodsOrder::PAID)
                           ->first();
        if($order)
        {
            RefundOrder::create([
                'user_id'       => TokenService::getCurrentUid(),
                'order_id'      => $order_id,
                'refundNumber'  => $order['prepay_id'],
                'refund_reason' => $refund_reason,

            ]);
            return true;
        }
        throw new AppException('该订单不能退款');
    }

    /**
     * @param $id
     * @return bool
     * @throws AppException
     * 退款
     */
    public function refund($id)
    {
        $record = RefundOrder::leftJoin('user as u','u.id','=','refund_order.user_id')
                             ->leftJoin('goods_order as g','g.id','=','refund_order.order_id')
                             ->where('refund_order.id',$id)
                             ->select('u.openid','refund_order.refundNumber',
                                 'g.prepay_id','g.sale_price','g.id as order_id'
                             )
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

    /**
     * @param $id
     * @param $refuse_reason
     * 拒绝
     */
    public function refuse($id,$refuse_reason)
    {
        RefundOrder::where('id',$id)
                   ->update([
                       'refuse_reason' => $refuse_reason,
                       'isAgree'       => RefundOrder::DISAGREE
                   ]);
    }
}