<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 18:30
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\User\ShoppingCart;
use App\Services\Token\TokenService;
use Illuminate\Support\Facades\Cache;

class CartService
{

    private $openid;
    private $key;

    public function __construct()
    {
        $this->openid = TokenService::getCurrentTokenVar('openid');
        $this->key = 'cart' . $this->openid;
        $val = Cache::get($this->key);
        if (empty($val)) {
            $data = $this->getInfoDb();
            $this->saveInfoCache($data);
        }
    }

    /**
     * @param $value
     * 将购物车的信息存入缓存
     */
    private function saveInfoCache($value)
    {
        Cache::put($this->key,$value,120);
    }

    private function getInfoCache()
    {
        $data = Cache::get($this->key);
        return $data;
    }

    /**
     * @return mixed
     * 从数据库中获取真实的购物车信息
     */
    public function getInfoDb()
    {
        $info = ShoppingCart::leftJoin('goods as g','g.id','=','shopping_cart.goods_id')
                                     ->where('shopping_cart.user_id',TokenService::getCurrentUid())
                                     ->orderByDesc('shopping_cart.updated_at')
                                     ->select('g.id','g.name','g.thu_url','g.stock','g.price',
                                         'shopping_cart.*')
                                     ->get();
        //转换购物车商品勾选状态 0=>false 1=>true
        $data['goods'] = $this->changeStatus($info);
        //计算购物车总价
        $data = $this->getPrice($data['goods']);
        return $data;
    }

    /**
     * 获取购物车信息
     */
    public function info()
    {
        $data = $this->getInfoCache();
        return $data;
    }

    /**
     * @param $data
     * 加入购物车
     */
    public function add($data)
    {
        //将缓存购物车入库
        //判断购物车是否存在该条记录(存在只需要修改数量)
        //不存在(新增记录)
        $this->saveDb();
        foreach ($data as $item)
        {
            $record = ShoppingCart::where('goods_id',$item['goods_id'])
                                  ->where('user_id',TokenService::getCurrentUid())
                                  ->first();
            if($record)
            {
                $record['count'] += $item['count'];
                $record['isSelect'] = ShoppingCart::SELECTED;
                $record->save();
            }
            else
            {
                ShoppingCart::create([
                    'user_id'  => TokenService::getCurrentUid(),
                    'goods_id' => $item['goods_id'],
                    'count'    => $item['count']
                ]);
            }
        }
    }

    /**
     * @param $id
     * @throws AppException
     * 删除购物车信息
     */
    public function delete($id)
    {
        //先将购物车信息入库
        //在数据库中删除该条记录
        $this->saveDb();
        $result = ShoppingCart::where('id',$id)
                              ->where('user_id',TokenService::getCurrentUid())
                              ->delete();
        if(!$result)
        {
            throw new AppException('删除失败');
        }
    }

    /**
     * @param $id
     * @param $count
     * @return mixed
     * @throws AppException
     * 修改购物车数量
     */
    public function change($id,$count)
    {
        //标记状态
        //取出购物车信息
        //遍历信息,如果存在该条记录,则改变状态
        //判断状态,如果存在该条记录,则重新计算购物车总价以及数量并将新的记录存于缓存
        //如果不存在,抛异常(执行此操作必定是在购物车界面,所以如果不存在该条记录必定出现异常)
        $status =
            [
                'isExist' => false
            ];
        $info = $this->getInfoCache();
        foreach ($info['goods'] as $item)
        {
            if($item['id'] == $id)
            {
                $item['count'] = $count;
                $status['isExist'] = true;
            }
        }
        if($status['isExist'])
        {
            $data = $this->getPrice($info['goods']);
            $this->saveInfoCache($data);
            return $data;
        }
        throw new AppException('购物车中没有该物品');
    }

    /**
     * @param $id
     * @param $select
     * @throws AppException
     * 修改购物车记录的勾选状态
     */
    public function select($id,$select)
    {
        //标记状态并取出购物车信息
        //遍历 如果存在该条记录,判断此时勾选状态
        //1=>true,0=>false  (前端之前传的是1,2,现在修改)
        //再将新的信息存于缓存
        $status = [
                'isExist' => false
            ];
        $info = $this->getInfoCache();
        foreach ($info['goods'] as $item)
        {
            if($item['id'] == $id)
            {
                switch ($select)
                {
                    case ShoppingCart::SELECTED:
                        $item['isSelect'] = true;
                        break;
                    case ShoppingCart::NOT_SELECT:
                        $item['isSelect'] = false;
                        break;
                }
                $status['isExist'] = true;
            }
        }
        if(!$status['isExist'])
        {
            throw new AppException( '购物车中没有该商品');
        }
        $this->saveInfoCache($info);
    }

    /**
     * @param $select
     * 全选
     */
    public function selectAll($select)
    {
        $info = $this->getInfoCache();
        foreach ($info['goods'] as $item) {
            switch ($select)
            {
                case ShoppingCart::SELECTED:
                    $item['isSelect'] = true;
                    break;
                case ShoppingCart::NOT_SELECT:
                    $item['isSelect'] = false;
                    break;
            }
        }
        $this->saveInfoCache($info);
    }

    /**
     * 将数据存入购物车
     */
    public function saveDb()
    {
        $data = Cache::get($this->key);
        if($data)
        {
            foreach ($data['goods'] as $item)
            {
                $record = ShoppingCart::where('id',$item['id'])
                                      ->first();
                $record['count'] = (int)$item['count'];
                if($item['isSelect'])
                {
                    $item['isSelect'] = ShoppingCart::SELECTED;
                }
                else
                {
                    $item['isSelect'] = ShoppingCart::NOT_SELECT;
                }
                $record->save();
            }
            $this->cacheDelete();
        }

    }

    /**
     *删除购物车缓存
     */
    private function cacheDelete()
    {
        Cache::forget($this->key);
    }

    /**
     * @param $data
     * @return mixed
     * 修改购物车状态
     */
    private function changeStatus($data)
    {
        foreach ($data as $item)
        {
            if($item['isSelect'] == ShoppingCart::NOT_SELECT)
            {
                $item['isSelect'] = false;
            }
            else
            {
                $item['isSelect'] = true;
            }
        }
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * 计算购物车价格以及数量
     */
    private function getPrice($data)
    {
        $result['totalPrice'] = $result['count'] = 0;
        $result['goods'] = $data;
        foreach ($data as $item)
        {
            if($item['isSelect'])
            {
                $result['totalPrice'] += $item['price'] * $item['count'];
                $result['count'] += $item['count'];
            }
        }
        return $result;
    }
}