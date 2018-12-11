<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/1
 * Time: 16:02
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Goods\Goods;
use App\Models\Goods\GoodsImage;
use App\Models\Goods\GoodsLabel;
use App\Models\Goods\Recommend;

define('add',0);
define('change',1);

define('TOP',0);
define('BOTTOM',1);
define('START_RECOMMEND',1);

class GoodsService
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

    /**
     * @param $category
     * @param $limit
     * @return mixed
     * 分类下所有商品
     */
    public function category($category,$limit)
    {
        $data = $this->query()
                     ->where('category_id',$category)
                     ->where('isShelves', Goods::SHELVES)
                     ->orderByDesc('created_at')
                     ->paginate($limit);
        return $this->checkRecommend($data);
    }

    /**
     * @param $limit
     * @return mixed
     * 失效
     */
    public function failure($limit)
    {
        $data = $this->query()
                     ->where('isShelves', Goods::NOT_SHELVES)
                     ->orderByDesc('updated_at')
                     ->paginate($limit);

        return $data;
    }

    /**
     * @param $limit
     * @return mixed
     * 待审
     */
    public function pending($limit)
    {
        $data = Goods::where('isPending',Goods::PENDING)
                     ->orderByDesc('created_at')
                     ->select('id','name','goods_id','thu_url','stock',
                              'category_id','price','sale_price',
                              'isShelves','created_at')
                     ->paginate($limit);
        return $data;
    }

    /**
     * @param $id
     * 审核通过
     */
    public function pendingOperate($id)
    {
        Goods::where('id',$id)
             ->update([
                 'isPending' => Goods::NOT_PENDING
             ]);
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
                     ->where('id',$id)
                     ->first();
        return $data;
    }

    /**
     * @param $content
     * @param $limit
     * @return mixed
     * 搜索
     */
    public function search($content,$limit)
    {
        $goods = $this->query()
                      ->where('name', 'like', '%'.$content.'%')
                      ->orWhere('goods_id','like','%'.$content.'%')
                      ->orWhere('recommend_reason','like','%'.$content.'%')
                      ->orWhere('country','like','%'.$content.'%')
                      ->orWhere('shop','like','%'.$content.'%')
                      ->orderByDesc('created_at')
                      ->paginate($limit);
        return $goods;
    }

    /**
     * @param $id
     * 推荐或取消推荐商品
     */
    public function recommendOperate($id)
    {

        $record = Recommend::where('goods_id',$id)->first();
        if($record)
        {
            $record->delete();
        }
        else
        {
            $max = Recommend::max('order');
            Recommend::create([
                'goods_id' => $id,
                'order'    => $max + 1
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
     * @param $data
     * 修改信息
     */
    public function update($data)
    {
        unset($data['token']);
        Goods::where('id',$data['id'])
             ->update($data);
    }

    /**
     * @return $this
     * 针对审核通过的商品的查询语句
     */
    private function query()
    {
        $query = Goods::with('category')
                      ->with('label')
                      ->where('isPending','=',Goods::NOT_PENDING)
                      ->where('stock', '>', 0)
                      ->select('id','name','goods_id','thu_url','stock',
                              'category_id','price','sale_price','isShelves');
        return $query;
    }

    /**
     * @param $data
     * @return mixed
     * 检查是否为推荐商品
     */
    private function checkRecommend($data)
    {
        foreach ($data as $item)
        {
            $record = Recommend::where('goods_id',$item['id'])
                               ->first();
            if($record)
            {
                $item['isRecommend'] = true;
            }
            else
            {
                $item['isRecommend'] = false;
            }
        }
        return $data;
    }

    /**
     *
     */
    public function getExcel()
    {
        $res = (new ExcelToArray())->get();
        foreach ($res as $k => $v) {
            if ($k > 1) {
                $record = Goods::where('goods_id',$v[0])
                    ->first();
                if($record)
                {
                    $this->updateExcel($v);
                }
                else
                {
                    $this->addExcel($v);
                }
            }
        }
    }

    public function updateExcel($v)
    {
        $record = Goods::where('goods_id',$v[0])
                       ->first();
        switch ($v[1]){
            case '全球精酿':
                $record['category_id'] = 1;
                $record['brand'] = $v[7];
                $record['degrees'] = $v[8];
                break;
            case '低度轻饮':
                $record['category_id'] = 2;
                $record['type'] = $v[7];
                $record['degrees'] = $v[8];
                break;
            case '花式饮品':
                $record['category_id'] = 3;
                $record['type'] = $v[7];
                $record['specifications'] = $v[8];
                break;
            case '美味零食':
                $record['category_id'] = 4;
                $record['type'] = $v[7];
                $record['flavor'] = $v[8];
                break;
        }
        $record['goods_id'] = $v[0];
        $record['stock'] = $v[2];
        $record['name'] = $v[3];
        $record['delivery_place'] = $v[5];
        $record['country'] = $v[6];
        $record['purchase_price'] = $v[9];
        $record['logistics_standard'] = $v[10];
        $record['cost_price'] = $v[11];
        $record['reference_price'] = $v[12];
        $record['price'] = $v[14];
        $record['sale_price'] = $v[13];
        $record['recommend_reason'] = $v[15];
        $record['notice'] = $v[16];
        $record['channels'] = $v[17];
        $record['shop'] = $v[18];
        $record['purchase_address'] = $v[19];
        $record['thu_url'] = $v[20];
        $record['cov_url'] = $v[21];
        $record['det_url'] = $v[22];
        $record['share_url'] = $v[23];
        $record->save();
        $label = explode("；", $v[4]);
        GoodsLabel::where('goods_id',$record['id'])->delete();
        for($i=0;$i<sizeof($label);$i++){
            GoodsLabel::create([
                'label_name' => $label[$i],
                'goods_id' => $record['id']
            ]);
        }
    }

    public function addExcel($v)
    {
        if($v[1] == '全球精酿' || $v[1] == '低度轻饮'||$v[1] == '花式饮品'||$v[1] == '美味零食')
        {
            switch ($v[1]){
                case '全球精酿':
                    $data['category_id'] = 1;
                    $data['brand'] = $v[7];
                    $data['degrees'] = $v[8];
                    break;
                case '低度轻饮':
                    $data['category_id'] = 2;
                    $data['type'] = $v[7];
                    $data['degrees'] = $v[8];
                    break;
                case '花式饮品':
                    $data['category_id'] = 3;
                    $data['type'] = $v[7];
                    $data['specifications'] = $v[8];
                    break;
                case '美味零食':
                    $data['category_id'] = 4;
                    $data['type'] = $v[7];
                    $data['flavor'] = $v[8];
                    break;
            }
            $data['goods_id'] = $v[0];
            $data['stock'] = $v[2];
            $data['name'] = $v[3];
            $data['delivery_place'] = $v[5];
            $data['country'] = $v[6];
            $data['purchase_price'] = $v[9];
            $data['logistics_standard'] = $v[10];
            $data['cost_price'] = $v[11];
            $data['reference_price'] = $v[12];
            $data['price'] = $v[14];
            $data['sale_price'] = $v[13];
            $data['recommend_reason'] = $v[15];
            $data['notice'] = $v[16];
            $data['channels'] = $v[17];
            $data['shop'] = $v[18];
            $data['purchase_address'] = $v[19];
            $data['thu_url'] = $v[20];
            $data['cov_url'] = $v[21];
            $data['det_url'] = $v[22];
            $data['share_url'] = $v[23];
            $id = Goods::insertGetId($data);
            $label = explode("；", $v[4]);
            for($i=0;$i<sizeof($label);$i++){
                GoodsLabel::create([
                    'label_name' => $label[$i],
                    'goods_id' => $id
                ]);
            }

        }
        else{
            throw new AppException( '出错了,检查excel表');
        }

    }

    /**
     * 上传商品图片
     */
    public function image()
    {
        $res = (new ExcelToArray())->get();
        foreach ($res as $k => $v) {
            if ($k > 1) {
                $goods = Goods::where('goods_id',$v[0])
                    ->first();
                $record = GoodsImage::where('goods_id',$goods['id'])
                                    ->where('order',$v[2])
                                    ->first();
                if($record)
                {
                    $record['goods_id'] = $goods['id'];
                    $record['order'] = $v[2];
                    $record['url'] = $v[1];
                    $record->save();
                }
                else {
                    $record = new GoodsImage();
                    $record['goods_id'] = $goods['id'];
                    $record['order'] = $v[2];
                    $record['url'] = $v[1];
                    $record->save();
                }
            }
        }
    }


}