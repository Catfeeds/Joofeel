<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/9/12
 * Time: 12:27
 */
return [

    //虚拟头像

    'avatar' =>
        [
            0  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/0.jpg',
            1  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/1.jpg',
            2  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/2.jpg',
            3  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/3.jpg',
            4  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/4.jpg',
            5  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/5.jpg',
            6  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/6.jpg',
            7  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/7.jpg',
            8  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/8.jpg',
            9  => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/9.jpg',
            10 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/10.jpg',
            11 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/11.jpg',
            12 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/12.jpg',
            13 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/13.jpg',
            14 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/14.jpg',
            15 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/15.jpg',
            16 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/16.jpg',
            17 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/17.jpg',
            18 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/18.jpg',
            19 => 'https://jufeeloss.oss-cn-hangzhou.aliyuncs.com/avatar/19.jpg',
        ],

    //收货地址标签
    'label' =>
        [
            1 => '家',
            2 => '公司',
            3 => '学校'
        ],

    //商品类别
    'goods_category' =>
        [
            0 => '所有宝贝',
            1 => '精酿啤酒',
            2 => '预调酒水',
            3 => '休闲零食',
            4 => '花式饮料'
        ],

    //微信支付回调地址
    'redirect_notify' => 'jufeel.jufeeling.com/api/v1/pay/notify',

    //商品筛选条件
    'goods_condition' =>
        [

            0 =>
                [
                    0 => ['中国','日本','俄罗斯','英国'],

                ],
            1 =>
                [
                    0 => ['中国','日本','俄罗斯','英国'],
                    1 => ['百威','嘉士伯'],    //品牌
                    2 => ['10~20','20~30'],   //度数
                    'name' => ['国家','品牌','度数']
                ],
            2 =>
                [
                    0 => ['中国','日本','俄罗斯','英国'],
                    1 => ['鸡尾酒'],           //种类
                    2 => ['10~20','20~30'],   //度数
                    'name' => ['国家','种类','度数']
                ],
            3 =>
                [
                    0 => ['中国','日本','俄罗斯','英国'],
                    1 => ['苏打水','汽水','运动饮料'],     //种类
                    2 => ['10ml','20ml','30ml','40ml'],  //规格
                    'name' => ['国家','种类','规格']
                ],
            4 =>
                [
                    0 => ['中国','日本','俄罗斯','英国'],
                    1 => ['糕点'],  //种类
                    2 => ['甜味'],   //口味
                    'name' => ['国家','种类','口味']
                ],

        ],

    'oss_config' => [
        'accessKeyId'   => 'LTAIjfhhjAEa69tU',
        'accessKeySecret'  => 'z9jMoqELKVfFwzJUtJVsh304Cwq1LD',
        'endpoint'    => 'jufeeloss.oss-cn-hangzhou.aliyuncs.com',
        'bucket' => 'jufeeloss',
    ]

];