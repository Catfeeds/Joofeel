<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/26
 * Time: 9:11
 */

namespace App\Http\Controllers\Api\v3;

use App\Exceptions\AppException;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Controller;
use App\Models\Enter\OfficialGoods;
use App\Services\Enter\OfficialGoodsService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class OfficialGoodsController extends Controller
{
    private $service;

    public function __construct(Request $req,OfficialGoodsService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 新增
     */
    public function add()
    {
        try{
            $this->validateForm();
            $this->service->add(
                $this->request->input('thu_url'),
                $this->request->input('title'),
                $this->request->input('price'),
                $this->request->input('url'),
                $this->request->input('end_time')
            );
        }catch (\Exception $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 更新
     */
    public function update()
    {
        try{
            $this->validate($this->request,
                [
                    'id' => 'required|integer|exists:mysql_enter.official_goods,id'
                ]);
            $this->validateForm();
            $this->service->update(
                $this->request->input('id'),
                $this->request->input('thu_url'),
                $this->request->input('title'),
                $this->request->input('price'),
                $this->request->input('url'),
                $this->request->input('end_time')
            );
        }catch (\Exception $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * 添加或者修改的验证
     */
    protected function validateForm()
    {
        $this->validate($this->request,
            [
                'thu_url'  => 'required|string',
                'title'    => 'required|string|max:20',
                'price'    => 'required|numeric',
                'url'      => 'required|string',
                'end_time' => 'required|date'
            ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索
     */
    public function search()
    {
        $this->validate($this->request,
            [
               'content' => 'required'
            ]);
        $data = OfficialGoods::search(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 详情
     */
    public function info()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql_enter.official_goods,id'
            ]);
        $data = OfficialGoods::where('id',$this->request->input('id'))->first();
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取
     */
    public function get()
    {
        $data = OfficialGoods::get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 图片上传
     */
    public function upload()
    {
        try{
            $data['src'] =
                (new FileController($this->request))->upload('enterjoofeel','official');
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson($data);
    }
}