<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 13:16
 */

namespace App\Services\Goods;


use App\Exceptions\AppException;
use App\Models\Goods\Goods;
use App\Models\Goods\Recommend;

define('TOP',0);
define('BOTTOM',1);
define('START_RECOMMEND',1);

class RecommendService
{
    public function recommend($limit)
    {
        /**
         *获取推荐商品
         */
        $data = Recommend::leftJoin('goods as g','g.id','=','recommend.goods_id')
            ->where('g.isShelves',Goods::SHELVES)
            ->select('g.id','g.name','g.goods_id','g.thu_url','g.stock',
                'g.category_id as category','g.price','g.sale_price','g.isShelves','recommend.order')
            ->orderBy('recommend.order','asc')
            ->paginate($limit);
        foreach ($data as $item)
        {
            $max = Recommend::max('order');
            if($item['order'] == $max)
            {
                $item['last'] = true;
            }
            else
            {
                $item['last'] = false;
            }
        }
        return $data;
    }

    /**
     * @param $id
     * 推荐或取消推荐商品
     */
    public function recommendOperate($id)
    {
        $max = Recommend::max('order');
        $record = Recommend::where('goods_id',$id)->first();
        if($record)
        {
            for ($i=$record['order']+1;$i<=$max;$i++)
            {
                $recommend = Recommend::getByOrder($i);
                $recommend['order'] -= 1;
                $recommend->save();
            }
            $record->delete();
        }
        else
        {
            Recommend::create([
                'goods_id' => $id,
                'order'    => $max + 1
            ]);
        }
    }

    /**
     * @param $data
     * @throws AppException
     * 调整顺序
     */
    public function order($data)
    {
        $record = Recommend::getByOrder($data['order']);
        if($data['type'] == TOP)
        {
            if($record['order'] == START_RECOMMEND)
            {
                throw new AppException('不能上移');
            }
            $lastRecord = Recommend::getByOrder($record['order'] - 1);
            $record['order'] -= 1;
            $lastRecord['order'] += 1;
            $lastRecord->save();
        }
        else
        {
            $max = Recommend::max('order');
            if($record['order'] == $max)
            {
                throw new AppException('不能下移');
            }
            $nextRecord = Recommend::getByOrder($record['order'] + 1);
            $record['order'] += 1;
            $nextRecord['order'] -= 1;
            $nextRecord->save();
        }
        $record->save();
    }
}