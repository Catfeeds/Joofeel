<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 13:14
 */

namespace App\Http\Controllers\Api\v1\Goods;

use App\Http\Controllers\BaseController;
use App\Models\MiniProgram\Goods\GoodsLabel;
use App\Services\MiniProgram\Goods\LabelService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class LabelController extends BaseController
{
    private $service;
    public function __construct(Request $req,LabelService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    public function get()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.goods,id'
            ]);
        $data = GoodsLabel::where('goods_id',$this->request->input('id'))
                          ->get();
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 新增标签
     */
    public function add()
    {
        $this->validate($this->request,
            [
                'id'         => 'required|integer|exists:mysql.goods,id',
                'label_name' => 'required|string|max:10'
            ]);
        $this->service->add($this->request->all());
        return ResponseUtil::toJson();
    }

    public function update()
    {
        $this->validate($this->request,
            [
                'id'         => 'required|integer|exists:mysql.goods_label,id',
                'label_name' => 'required|string|max:10'
            ]);
        $this->service->update($this->request->all());
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 删除
     */
    public function delete()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.goods_label,id'
            ]);
         GoodsLabel::where('id',$this->request->input('id'))->delete();
         return ResponseUtil::toJson();
    }
}