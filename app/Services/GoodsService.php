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

class GoodsService
{
    public function recommend()
    {
        /**
         *推荐商品
         */
        $data = Recommend::leftJoin('goods as g','g.id','=','recommend.goods_id')
                         ->select('g.id','g.name','g.goods_id','g.thu_url','g.stock',
                             'g.category_id as category','g.price','g.sale_price')
                         ->get();
        return $data;
    }

    /**
     * @param $category
     * @return mixed
     * 分类下的全部商品(上架中)
     */
    public function category($category)
    {
        $data = $this->query()
            ->where('category_id',$category)
            ->where('isShelves', Goods::SHELVES)
            ->orderByDesc('created_at')
            ->get();
        return $data;
    }


    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     * 详情页
     */
    public function info($id)
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
     * @param $id
     * 推荐或取消推荐商品
     */
    public function recommendOperate($id)
    {
        $result = Recommend::where('goods_id',$id)
                           ->delete();
        if(!$result)
        {
            Recommend::create([
                'goods_id' => $id
            ]);
        }
    }

    /**
     * @param $id
     * 上下架商品
     */
    public function operate($id)
    {
        $record = Goods::where('id',$id)
                       ->first();
        if($record['isShelves'] == Goods::SHELVES)
        {
            $record['isShelves'] = Goods::NOT_SHELVES;
        }
        else
        {
            $record['isShelves'] = Goods::SHELVES;
        }
        $record->save();
    }

    /**
     * @return mixed
     * 库存紧张的商品
     */
    public function oos()
    {
        $data = $this->query()
                     ->where('stock','<',30)
                     ->where('isShelves',Goods::SHELVES)
                     ->get();
        return $data;
    }


    /**
     * @return $this
     */
    private function query()
    {
        $query = Goods::with('category')
                      ->with('label')
                      ->where('stock', '>', 0)
                      ->select('name','thu_url','price',
                          'sale_price','category_id','id','shop','isShelves');
        return $query;
    }
}