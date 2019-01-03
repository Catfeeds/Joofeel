<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/4
 * Time: 18:36
 */

namespace App\Utils;


class Map
{
    /**
     * @param $address
     * @return mixed
     * 获取经纬度
     */
    static function getLngLat($address){
        $data =
            [
                'address' => $address,
                'ak'      => config('map.ak'),
                'output'  => 'json'
            ];
        $url = config('map.baidu_map_url').'?'.http_build_query($data);
        $result = json_decode(Curl::curl_get($url),true);
        return $result;
    }

}