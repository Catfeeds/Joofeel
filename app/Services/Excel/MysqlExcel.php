<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/21
 * Time: 15:27
 */

namespace App\Services\Excel;

use App\Models\Banner;
use App\Models\Coupon\Coupon;
use App\Models\Goods\Goods as GoodsModel;
use App\Models\Goods\GoodsCategory;
use App\Models\Goods\GoodsLabel;
use App\Models\Order\GoodsOrder;
use App\Models\Party\Message;
use App\Models\User\DeliveryAddress;

class MysqlExcel
{
    public function sqlGoods($res)
    {
        foreach ($res as $k => $v) {
            if ($k > 0) {
                GoodsModel::create([
                    'id'      => $v[0],
                    'goods_id' => $v[1],
                    'name' => $v[2],
                    'category_id' => $v[3],
                    'stock' => $v[4],
                    'notice' => $v[5],
                    'carriage' => $v[6],
                    'recommend_reason' => $v[7],
                    'channels' => $v[8],
                    'purchase_address' => $v[9],
                    'shop' => $v[10],
                    'delivery_place' => $v[11],
                    'logistics_standard' => $v[12],
                    'purchase_price' => $v[13],
                    'cost_price' => $v[14],
                    'reference_price' => $v[15],
                    'price' => $v[16],
                    'sale_price' => $v[17],
                    'country' => $v[18],
                    'brand' => $v[19],
                    'degrees' => $v[20],
                    'type' => $v[21],
                    'specifications' => $v[22],
                    'flavor' => $v[23],
                    'thu_url' => $v[24],
                    'cov_url' => $v[25],
                    'det_url' => $v[26],
                    'isShelves' => $v[27],
                    'sold'   => $v[30],
                    'created_at' => date('Y-m-d H:i:s',$v[28]),
                    'updated_at' => date('Y-m-d H:i:s',$v[29]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 导入banner表数据
     */
    public function sqlBanner($res)
    {
        foreach ($res as $k => $v) {
            if ($k > 0) {
                Banner::create([
                    'id'      => $v[0],
                    'image' => $v[1],
                    'isShow' => $v[2],
                    'created_at' => date('Y-m-d H:i:s',$v[3]),
                    'updated_at' => date('Y-m-d H:i:s',$v[4]),
                    'type'  => Banner::GOODS_DETAIL,
                    'url' => $v[5]
                ]);
            }
        }
    }

    /**
     * @param $res
     * 购物券
     */
    public function sqlCoupon($res)
    {
        foreach ($res as $k => $v) {
            if ($k > 0) {
                Coupon::create([
                    'id'      => $v[0],
                    'name' => $v[1],
                    'rule' => $v[2],
                    'sale' => $v[3],
                    'category' => $v[4],
                    'count'  => $v[5],
                    'isReceive' => $v[6],
                    'start_time' => $v[7],
                    'end_time' => $v[8]
                ]);
            }
        }
    }

    /**
     * @param $res
     * 收货地址
     */
    public function sqlDeliveryAddress($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                DeliveryAddress::create([
                    'id'      => $v[0],
                    'user_id' => $v[1],
                    'receipt_name' => $v[2],
                    'receipt_area' => $v[3],
                    'receipt_address' => $v[4],
                    'receipt_phone'  => $v[5],
                    'label'  => $v[6],
                    'isDefault' => $v[7],
                ]);
            }
        }
    }

    /**
     * @param $res
     * 商品分类
     */
    public function sqlGoodsCategory($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                GoodsCategory::create([
                    'id'      => $v[0],
                    'name' => $v[1],
                ]);
            }
        }
    }

    /**
     * @param $res
     * 导入商品标签
     */
    public function sqlGoodsLabel($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                GoodsLabel::create([
                    'goods_id' => $v[1],
                    'label_name'  => $v[2]
                ]);
            }
        }
    }

    /**
     * @param $res
     * 订单表
     */
    public function sqlGoodsOrder($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                GoodsOrder::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'order_id'  => $v[2],
                    'tracking_id'  => $v[3],
                    'prepay_id'  => $v[4],
                    'price'  => $v[5],
                    'sale_price'  => $v[6],
                    'sale'  => $v[7],
                    'carriage'  => $v[8],
                    'receipt_id'  => $v[9],
                    'coupon_id'  => $v[10],
                    'receipt_name'  => $v[11],
                    'receipt_address'  => $v[12],
                    'receipt_phone'  => $v[13],
                    'isSign'  => $v[14],
                    'isDeleteUser'  => $v[15],
                    'isPay'  => $v[16],
                    'isDeleteAdmin'  => $v[17],
                    'created_at' => date('Y-m-d H:i:s',$v[18]),
                    'updated_at' => date('Y-m-d H:i:s',$v[19]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 留言
     */
    public function sqlMessage($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                Message::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'party_id'  => $v[2],
                    'content' => $v[3],
                    'created_at' => date('Y-m-d H:i:s',$v[4]),
                    'updated_at' => date('Y-m-d H:i:s',$v[5]),
                ]);
            }
        }
    }

}