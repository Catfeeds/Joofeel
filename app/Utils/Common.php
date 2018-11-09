<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/23
 * Time: 14:28
 */

namespace App\Utils;


class Common
{
     static function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }

    /**
     * @param $data
     * @return mixed
     * 获取收获地址标签
     */
   static function getAddressLabel($data){
        $label = config('jufeel_config.label');
        foreach ($data as $d){
            $d['label'] = $label[$d['label']];
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 获取优惠券类别
     */
    static function getCouponCategory($data){
        $goods_category = config('jufeel_config.goods_category');
        for($i=0;$i<sizeof($data);$i++){
            $data[$i]['category'] = $goods_category[$data[$i]['category']];
        }
        return $data;
    }

    /**
     * @param $phone
     * @return bool
     * 验证手机号
     */
    static function verifyPhone($phone)
    {
        $isMob="/^1[34578]{1}\d{9}$/";
        $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
        if(!preg_match($isMob,$phone) && !preg_match($isTel,$phone)){
            return false;
        }else{
            return true;
        }
    }

    static function getWeeks($time = '', $format='Y-m-d'){
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i=1; $i<=7; $i++){
            $date[$i] = date($format ,strtotime( '+' . $i-7 .' days', $time));
        }
        return $date;
    }

}