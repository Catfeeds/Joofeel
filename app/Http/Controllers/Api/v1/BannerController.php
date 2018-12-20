<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 11:48
 */

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\AppException;
use App\Http\Controllers\BaseController;
use App\Models\MiniProgram\Banner;
use App\Services\MiniProgram\BannerService;
use App\Services\FileService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    private $service;
    public function __construct(Request $req,BannerService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }



    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有轮播图
     */
    public function all()
    {
        $data = Banner::orderBy('isShow','asc')
                      ->paginate($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取
     */
    public function get()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.banner,id'
            ]);
        $data = Banner::get($this->request->input('id'));
        return ResponseUtil::toJson($data);
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     * 添加
     */
    public function add()
    {
        $this->validate($this->request,
            [
                'image'   => 'required|string',
                'isPrize' => 'required|integer|in:0,1',
                'type'    => 'required|integer|in:1,2,3',
                'url'     => 'required'
            ]);
        try{
            $this->service->add(
                $this->request->input('image'),
                $this->request->input('isPrize'),
                $this->request->input('type'),
                $this->request->input('url')
            );
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 上下架banner
     */
    public function operate()
    {
        $this->validate($this->request,
            [

                'id' => 'required|integer|exists:mysql.banner,id'
            ]);
        try{
            $this->service->operate($this->request->input('id'));
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }

        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 修改
     */
    public function update()
    {
        $this->validate($this->request,
            [
                'id'      => 'required|integer|exists:mysql.banner,id',
                'image'   => 'required|string',
                'isPrize' => 'required|integer|in:0,1',
                'type'    => 'required|integer|in:1,2,3',
                'url'     => 'required'
            ]);
        $data = $this->request->all();
        unset($data['file']);
        unset($data['token']);
        Banner::where('id',$this->request->input('id'))->update($data);
        return ResponseUtil::toJson();
    }

    /**
     * @param Request $request
     * @return string
     * @throws AppException
     * banner上传
     */
    public function upload(Request $request)
    {
        $data = (new FileService())->upload($request,'banner');
        return ResponseUtil::toJson($data);
    }
}