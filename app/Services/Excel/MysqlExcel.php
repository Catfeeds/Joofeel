<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/21
 * Time: 15:27
 */

namespace App\Services\Excel;

use App\Models\Banner;
use App\Models\Goods\Goods as GoodsModel;

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

    public function sqlAdmin()
    {

    }

    public function sqlCoupon()
    {

    }

    public function sqlDeliveryAddress()
    {

    }

}