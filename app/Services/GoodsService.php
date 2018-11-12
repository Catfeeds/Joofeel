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
use App\Models\Goods\GoodsLabel;
use App\Models\Goods\Recommend;

define('add',0);
define('change',1);

class GoodsService
{
    public function recommend($limit)
    {
        /**
         *推荐商品
         */
        $data = Recommend::leftJoin('goods as g','g.id','=','recommend.goods_id')
                         ->where('g.isShelves',Goods::SHELVES)
                         ->select('g.id','g.name','g.goods_id','g.thu_url','g.stock',
                             'g.category_id as category','g.price','g.sale_price','g.isShelves')
                         ->paginate($limit);
        return $data;
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
     * 得到Excel并处理
     */
    public function getExcel($type)
    {
        if (!empty ($_FILES ['file'] ['name'])) {
            $tmp_file = $_FILES ['file'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file'] ['name']);
            $file_type = $file_types [count($file_types) - 1];
            if (strtolower($file_type) != "xlsx") {
                throw new AppException('不是Excel文件，重新上传');
            }
            $savePath = base_path('public/uploads/');
            $str = date('Ymdhis');
            $file_name = $str . "." . $file_type;
            if (!copy($tmp_file, $savePath . $file_name)) {
                throw new AppException('上传失败');
            }
            $ExcelToArray = new ExcelToArray();//实例化
            $res = $ExcelToArray->read($savePath . $file_name, "UTF-8", $file_type);//传参,判断office2007还是office2003
            if($type == add)
            {
                $this->createExcelGoods($res);
            }
            else if($type == change)
            {
                $this->changeExcelGoods($res);
            }

           //删除本地Excel
            unlink(base_path('public/uploads/' . $file_name));
            return true;
        }
        throw new AppException('请上传文件');
    }

    /**
     * @param $excel
     * 修改
     */
    private function changeExcelGoods($excel)
    {
        foreach ($excel as $k => $v) {
            if ($k > 1) {
                $goods = Goods::where('goods_id',$v[0])
                              ->first();
                $goods['goods_id'] = $v[0];
                $goods['stock'] = $v[2];
                $goods['name'] = $v[3];
                $goods['delivery_place'] = $v[5];
                $goods['country'] = $v[6];
                $goods['purchase_price'] = $v[9];
                $goods['logistics_standard'] = $v[10];
                $goods['cost_price'] = $v[9] + $v[10];
                $goods['reference_price'] = $v[12];
                $goods['price'] = $v[13];
                $goods['sale_price'] = $goods['price'];
                $goods['recommend_reason'] = $v[14];
                $goods['notice'] = $v[15];
                $goods['channels'] = $v[16];
                $goods['shop'] = $v[17];
                $goods['purchase_address'] = $v[18];
                $goods['thu_url'] = $v[19];
                $goods['cov_url'] = $v[20];
                $goods['det_url'] = $v[21];
                $goods['created_at'] = $goods['updated_at'] = date('Y-m-d H:i:s');
                $goods->save();
            }
        }
    }

    /**
     * @param $excel
     * 入库开始
     */
    private function createExcelGoods($excel)
    {
        foreach ($excel as $k => $v) {
            if ($k > 1) {
                switch ($v[1]){
                    case '精酿啤酒':
                        $data[$k]['category_id'] = 1;
                        $data[$k]['brand'] = $v[7];
                        $data[$k]['degrees'] = $v[8];
                        break;
                    case '预调酒水':
                        $data[$k]['category_id'] = 2;
                        $data[$k]['type'] = $v[7];
                        $data[$k]['degrees'] = $v[8];
                        break;
                    case '花式饮料':
                        $data[$k]['category_id'] = 3;
                        $data[$k]['type'] = $v[7];
                        $data[$k]['specifications'] = $v[8];
                        break;
                    case '休闲零食':
                        $data[$k]['category_id'] = 4;
                        $data[$k]['type'] = $v[7];
                        $data[$k]['flavor'] = $v[8];
                        break;
                }
                $data[$k]['goods_id'] = $v[0];
                $data[$k]['stock'] = $v[2];
                $data[$k]['name'] = $v[3];
                $data[$k]['delivery_place'] = $v[5];
                $data[$k]['country'] = $v[6];
                $data[$k]['purchase_price'] = $v[9];
                $data[$k]['logistics_standard'] = $v[10];
                $data[$k]['cost_price'] = $v[9] + $v[10];
                $data[$k]['reference_price'] = $v[12];
                $data[$k]['price'] = $v[13];
                $data[$k]['sale_price'] = $data[$k]['price'];
                $data[$k]['recommend_reason'] = $v[14];
                $data[$k]['notice'] = $v[15];
                $data[$k]['channels'] = $v[16];
                $data[$k]['shop'] = $v[17];
                $data[$k]['purchase_address'] = $v[18];
                $data[$k]['thu_url'] = $v[19];
                $data[$k]['cov_url'] = $v[20];
                $data[$k]['det_url'] = $v[21];
                $data[$k]['isPending'] = Goods::PENDING; //处于待审状态
                $data[$k]['isShelves'] = Goods::SHELVES;
                $data[$k]['created_at'] = $data[$k]['updated_at'] = date('Y-m-d H:i:s');
                $id = Goods::insertGetId($data[$k]);
                $label = explode("；", $v[4]);
                for($i=0;$i<sizeof($label);$i++){
                    GoodsLabel::create([
                        'label_name' => $label[$i],
                        'goods_id' => $id
                    ]);
                }
            }
        }
    }
}