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
use App\Models\Goods\Recommend;
use App\Models\Order\GoodsOrder;
use App\Models\Order\OrderId;
use App\Models\Party\Message;
use App\Models\Party\Party;
use App\Models\Party\PartyOrder;
use App\Models\Prize\Prize;
use App\Models\Prize\PrizeOrder;
use App\Models\Title;
use App\Models\User\DeliveryAddress;
use App\Models\User\ShoppingCart;
use App\Models\User\User;
use App\Models\User\UserCoupon;

class MysqlExcel
{
    /**
     * @param $res
     * 商品
     */
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

    /**
     * @param $res
     * 订单详情
     */
    public function sqlOrderId($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {

                OrderId::create([
                    'id' => $v[0],
                    'order_id' => $v[1],
                    'goods_id'  => $v[2],
                    'user_id' => $v[3],
                    'party_id' => $v[4],
                    'isPay' => $v[5],
                    'isDeleteUser' => $v[6],
                    'isSelect' => $v[7],
                    'count' => $v[9],
                    'price' => $v[8],
                    'created_at' => date('Y-m-d H:i:s',$v[10]),
                    'updated_at' => date('Y-m-d H:i:s',$v[11]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 聚会表导入
     */
    public function sqlParty($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                //Excel日期时间时间戳与php不一致,需转换一次
                $excelTime = $v[7] + $v[8];
                $time = ($excelTime - 25569) * 24*60*60;
                Party::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'image'  => $v[2],
                    'description' => $v[3],
                    'way' => $v[4],
                    'people_no' => $v[6],
                    'remaining_people_no' => $v[5],
                    'date' => date('Y-m-d',$time),
                    'time' => date('H:i',$time-8*60*60),
                    'site' => $v[9],
                    'longitude' => $v[10],
                    'latitude' => $v[11],
                    'created_at' => date('Y-m-d H:i:s',$v[12]),
                    'updated_at' => date('Y-m-d H:i:s',$v[13]),
                    'start_time' => $v[14],
                    'isDeleteAdmin' =>$v[15],
                    'isDeleteUser' => $v[16],
                    'isClose' => $v[17]
                ]);
            }
        }
    }

    /**
     * @param $res
     * 派对订单表导入
     */
    public function sqlPartyOrder($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                PartyOrder::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'party_id'  => $v[2],
                    'isDeleteUser' => $v[3],
                    'created_at' => date('Y-m-d H:i:s',$v[4]),
                    'updated_at' => date('Y-m-d H:i:s',$v[5]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 抽奖表导入
     */
    public function sqlPrize($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                Prize::create([
                    'id' => $v[0],
                    'goods_id' => $v[1],
                    'open_prize_time'  => $v[2],
                    'isPrize' => $v[3],
                    'created_at' => date('Y-m-d H:i:s',$v[4]),
                    'updated_at' => date('Y-m-d H:i:s',$v[5]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 抽奖订单表
     */
    public function sqlPrizeOrder($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                PrizeOrder::create([
                    'id' => $v[0],
                    'prize_id' => $v[1],
                    'user_id'  => $v[2],
                    'form_id'  => $v[3],
                    'isLucky' => $v[4],
                    'created_at' => date('Y-m-d H:i:s',$v[5]),
                    'updated_at' => date('Y-m-d H:i:s',$v[6]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 推荐表
     */
    public function sqlRecommend($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                Recommend::create([
                    'id' => $v[0],
                    'goods_id' => $v[1],
                    'created_at' => date('Y-m-d H:i:s',$v[2]),
                    'updated_at' => date('Y-m-d H:i:s',$v[3]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 推荐表
     */
    public function sqlCart($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                ShoppingCart::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'goods_id' => $v[2],
                    'count' => $v[3],
                    'isSelect' => $v[4],
                    'created_at' => date('Y-m-d H:i:s',$v[5]),
                    'updated_at' => date('Y-m-d H:i:s',$v[6]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 推荐标题表
     */
    public function sqlTitle($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                Title::create([
                    'id' => $v[0],
                    'content' => $v[1],
                    'isShow' => $v[2],
                ]);
            }
        }
    }

    /**
     * @param $res
     * 用户表
     */
    public function sqlUser($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                User::create([
                    'id' => $v[0],
                    'openid' => $v[1],
                    'nickname' => $v[2],
                    'avatar' => $v[3],
                    'isNewUser' => $v[4],
                    'created_at' => date('Y-m-d H:i:s',$v[5]),
                    'updated_at' => date('Y-m-d H:i:s',$v[6]),
                ]);
            }
        }
    }

    /**
     * @param $res
     * 用户购物券
     */
    public function sqlUserCoupon($res)
    {
        foreach ($res as $k => $v)
        {
            if($k>0)
            {
                UserCoupon::create([
                    'id' => $v[0],
                    'user_id' => $v[1],
                    'coupon_id' => $v[2],
                    'state' => $v[3],
                    'status' => $v[4],
                    'start_time' => $v[5],
                    'end_time' => $v[6]
                ]);
            }
        }
    }
}