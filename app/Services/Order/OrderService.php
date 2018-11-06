<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 18:28
 */

namespace App\Services\Order;

use App\Exceptions\AppException;
use App\Models\Goods\Goods;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\User\DeliveryAddress;
use App\Models\User\ShoppingCart;
use App\Models\User\UserCoupon;
use App\Services\BaseService;
use App\Services\CartService;
use App\Services\Token\TokenService;

const minPayPrice = 0.01;

class OrderService extends BaseService
{
    private $oGoods;    //订单商品信息
    private $Goods;     //真实商品信息
    private $couponId;  //购物券id
    private $receiptId; //收货地址id
    private $carriage;

    public function generate($oGoods,$couponId,$carriage,$receiptId)
    {
        $this->oGoods = $oGoods;
        $this->couponId = $couponId;
        $this->carriage = $carriage;
        $this->receiptId = $receiptId;
        $this->Goods = $this->getGoodsByOrder($oGoods);
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            throw new AppException('抱歉,已没有库存');
        }
        $orderSnap = $this->snapOrder($status);
        $order = $this->create($orderSnap);
        return $order;
    }

    /**
     * @param $orderSnap
     * @return mixed
     * 生成订单逻辑
     */
    public function create($orderSnap)
    {
        //获取优惠价格
        //将总订单入库
        //找到单个商品信息
        //减库存
        //删购物车记录(如果存在)
        //生成单个商品的订单记录
        //返回总订单id
        $sale = $this->getSale();
        $orderId = $this->save($orderSnap,$sale);
        foreach ($this->oGoods as $key => $item)
        {
            $g = Goods::where('id',$item['goods_id'])
                      ->select('id','stock','sale_price')
                      ->first();
            $g['stock'] -= $item['count'];
            $g->save();
            $this->deleteCartRecord($g['id']);
            OrderId::create([
                'price'    => $g['sale_price'],
                'count'    => $item['count'],
                'user_id'  => $this->uid,
                'order_id' => $orderId,
                'goods_id' => $g['id'],
            ]);
        }
        return $orderId;
    }

    /**
     * @param $id
     * 下订单时删除购物车记录
     */
    public function deleteCartRecord($id)
    {
        ShoppingCart::where('user_id',$this->uid)
                    ->where('goods_id',$id)
                    ->delete();
    }

    /**
     * @return int
     * 获取优惠价格并修改优惠券状态
     */
    private function getSale()
    {
        $sale = 0;
        $coupon = UserCoupon::with('coupon')
                            ->where('user_id',$this->uid)
                            ->where('coupon_id',$this->couponId)
                            ->first();
        if($coupon)
        {
            $coupon->status = UserCoupon::USED;
            $coupon->save();
            $sale = $coupon['coupon']['sale'];
        }
        return $sale;
    }

    /**
     * @param $orderSnap
     * @param $sale
     * @return mixed
     * @throws AppException
     * 入库
     */
    private function save($orderSnap,$sale)
    {
        if($orderSnap['price'] - $sale <= minPayPrice)
        {
            throw new AppException('支付价格出错');
        }
        $data =
            [
                'sale'            => $sale,
                'price'           => $orderSnap['price'],
                'user_id'         => $this->uid,
                'carriage'        => $this->carriage,
                'order_id'        => $this->makeOrderNo(),
                'coupon_id'       => $this->couponId,
                'sale_price'      => $orderSnap['price'] - $sale,
                'receipt_id'      => $this->receiptId,
                'created_at'      => time(),
                'updated_at'      => time(),
                'receipt_name'    => $orderSnap['receipt']['receipt_name'],
                'receipt_phone'   => $orderSnap['receipt']['receipt_phone'],
                'receipt_address' => $orderSnap['receipt']['receipt_address'],

            ];
        $orderId = GoodsOrder::insertGetId($data);
        return $orderId;
    }

    /**
     * @param $oGoods
     * @return array
     * 通过订单获得真实的商品信息
     */
    private function getGoodsByOrder($oGoods)
    {
        $Goods = [];
        foreach ($oGoods as $item)
        {
            $record = Goods::where('id',$item['id'])
                           ->select('id','stock','price','sale_price')
                           ->first();
            array_push($Goods,$record);
        }
        return $Goods;
    }

    /**
     * @return array
     * 订单状态
     */
    private function getOrderStatus()
    {
        $pStatus = [
            'orderPrice' => 0,
            'pass'       => true
        ];
        foreach ($this->oGoods as $oGood) {
            $gStatus = $this->getGoodsStatus($oGood['goods_id'], $oGood['count'], $this->Goods);
            if (!$gStatus['haveStock']) {
                $status['pass'] = false;
            }
            $pStatus['orderPrice'] += $gStatus['totalPrice'];
        }
        return $pStatus;
    }


    /**
     * @param $oGid
     * @param $oCount
     * @param $Goods
     * @return array
     * @throws AppException
     * 获取商品的状态
     */
    private function getGoodsStatus($oGid,$oCount,$Goods)
    {
        $pIndex = -1;
        $pStatus = [
            'haveStock' => false,
            'totalPrice' => 0
        ];
        foreach ($Goods as $key => $item)
        {
            if($oGid == $item['id'])
            {
                $pIndex = $key;
            }
        }
        if($pIndex == -1)
        {
            throw new AppException('id为' . $oGid . '的商品不存在,创建订单失败');
        }
        $g = $Goods[$pIndex];
        $pStatus['totalPrice'] = $g['sale_price'] * $oCount;
        if ($g['stock'] - $oCount >= 0) {
            $pStatus['haveStock'] = true;
        }
        return $pStatus;
    }

    /**
     * @param $status
     * @return mixed
     * 订单快照
     */
    private function snapOrder($status)
    {
        $snap['price'] = $status['orderPrice'];
        $snap['receipt'] = DeliveryAddress::find($this->receiptId);
        return $snap;
    }

    /**
     * @param $order_id
     * @return array
     * 根据订单号检查产品的库存
     */
    public function checkOrderStock($order_id)
    {
        $Goods = OrderId::where('order_id', $order_id)->get();
        $this->oGoods = $Goods;
        $this->Goods = $this->getGoodsByOrder($this->oGoods);
        $status = $this->getOrderStatus();
        return $status;
    }

    /**
     * 生成订单号
     */
    private function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * @param $goods
     * @return mixed
     * 获取预订单信息
     */
    public function pre($goods)
    {
        (new CartService())->saveDb();
        $data['goods'] = [];
        $data['carriage'] = $data['goods_count'] = 0;
        foreach ($goods as $key => $item)
        {
            $g = Goods::where('id', $item['goods_id'])
                      ->select('id','name','category_id','price',
                          'sale_price','stock','thu_url','carriage')
                      ->first();
            $g['count'] = $item['count'];
            array_push($data['goods'],$g);
            $data['carriage'] += $g['carriage'];
            $data['goods_count'] += $g['count'];
        }
        $data['address'] = DeliveryAddress::getDefaultAddress(TokenService::getCurrentUid());
        return $this->organizePreInfo($data);
    }

    /**
     * @param $data
     * @return int
     * 组装数据
     */
    private function organizePreInfo($data)
    {
        $data['totalPrice'] = 0;
        foreach ($data['goods'] as $item)
        {
            $data['totalPrice'] += $item['price'] * $item['count'];
        }
        return $this->getOrderCoupon($data);
    }

    /**
     * @param $data
     * @return mixed
     * 获取该订单可以用的优惠券
     */
    private function getOrderCoupon($data)
    {
        //先获取用户所有可用的优惠券
        //遍历优惠券
        //判断优惠券的种类
        //如果是所有商品类,则直接判断优惠券的规则是否达到订单的金额
        //如果不是所有商品类,则判断该分类下的订单的商品是否已达到规则
        //记录id
        $data['coupon'] = [];
        $coupon = UserCoupon::getCurrentCoupon($this->uid);
        foreach ($coupon as $key => $item )
        {
            $price = 0;
            if($item['category'] == UserCoupon::ALL)
            {
                foreach ($data['goods'] as $g)
                {
                    $price += $g['sale_price'] * $g['count'];
                }
            }
            else
            {
                foreach ($data['goods'] as $g)
                {
                    if($g['category_id'] == $item['category'])
                    {
                        $price += $g['sale_price'] * $g['count'];
                    }
                }
            }
            if($item['rule'] <= $price)
            {
                array_push($data['coupon'],$item['id']);
            }
        }
        return $data;
    }
}