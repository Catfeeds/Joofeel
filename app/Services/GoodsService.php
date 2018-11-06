<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 16:02
 */

namespace App\Services;

use App\Models\Goods\Goods;
use App\Models\Goods\Recommend;
use Illuminate\Support\Facades\Cache;

class GoodsService
{
    public function recommend()
    {
        /**
         *推荐商品
         */
        $data = Cache::get('recommend');
        if($data)
        {
            return $data;
        }
        $data = Recommend::with(['goods'=>function($query){
            $query->with('label')
                  ->select('id','name','thu_url','price','sale_price','category_id');
        }])
            ->select('goods_id')
            ->get();
        Cache::pull('recommend',$data,120);
        return $data;
    }

    /**
     * @param $category
     * @return mixed
     * 分类下的全部商品
     */
    public function category($category)
    {
        $data = Cache::get('goods' . $category);
        if($data)
        {
            return $data;
        }
        //全部商品
        if($category == 0)
        {
            $data = $this->query()
                         ->orderByDesc('created_at')
                         ->get();
        }
        else
        {
            $data = $this->query()
                         ->where('category_id',$category)
                         ->orderByDesc('created_at')
                         ->get();
        }
        Cache::pull('goods' . $category , $data , 120);
        return $data;
    }

    /**
     * @param $sort
     * @param $category
     * @param $value
     * @return mixed
     * 获取筛选后的商品
     */
    public function screening($sort,$category,$value)
    {
        $val = $this->getConditions($category,$value);
        $query = $this->query()
                      ->where($val);
        switch ($sort) {
            case Goods::DEFAULT_SORT:
                $goods['data'] = $query->orderBy('created_at','desc')
                                       ->get();
                break;
            case Goods::PRICE_ASC_SORT:
                $goods['data'] = $query->orderBy('price','asc')
                                       ->get();
                break;
            case Goods::PRICE_DESC_SORT:
                $goods['data'] = $query->orderBy('price','desc')
                                       ->get();
                break;
        }
        return $goods;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * 详情页
     */
    public function detail($id)
    {
        $data = Goods::with('category')
                     ->with('label')
                     ->select('id', 'name', 'stock', 'notice',
                         'carriage', 'category_id', 'recommend_reason',
                         'price', 'sale_price', 'country', 'brand',
                         'degrees', 'type', 'specifications', 'flavor',
                         'thu_url', 'cov_url', 'det_url')
                     ->first($id);
        return $data;
    }

    /**
     * @param $content
     * @return mixed
     * 搜索
     */
    public function search($content)
    {
        $goods = $this->query()
                      ->orderByDesc('created_at')
                      ->where('name', 'like', '%'.$content.'%')
                      ->orWhere('recommend_reason','like','%'.$content.'%')
                      ->orWhere('shop','like','%'.$content.'%')
                      ->get();
        return $goods;
    }

    /**
     * @param $category
     * @param $value
     * @return array
     * 得到筛选条件
     */
    private function getConditions($category,$value)
    {
        //根据商品分类得到不同的筛选条件
        //确定筛选条件后返回
        //将筛选条件为0的剔除数组
        switch ($category) {
            case 1:
                $data =
                    [
                        'brand' => $value[1],
                        'degree' => $value[2]
                    ];
                break;
            case 2:
                $data =
                    [
                        'type' => $value[1],
                        'degree' => $value[2]
                    ];
                break;
            case 3:
                $data =
                    [
                        'type' => $value[1],
                        'specifications' => $value[2]
                    ];
                break;
            case 4:
                $data =
                    [
                        'type' => $value[1],
                        'flavor' => $value[2]
                    ];
                break;
        }
        //将不符合条件的剔除
        $data['country'] = $value[0];
        $result = array_diff($data, [0]);
        return $result;
    }


    /**
     * @return $this
     */
    private function query()
    {
        $query = Goods::with('category')
                      ->with('label')
                      ->where('stock', '>', 0)
                      ->where('isShelves', Goods::SHELVES)
                      ->select('name','thu_url','price',
                          'sale_price','category_id','id','shop');
        return $query;
    }
}