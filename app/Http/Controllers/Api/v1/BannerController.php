<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 11:48
 */

namespace App\Http\Controllers\Api\v1;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\BannerService;
use App\Utils\ImgUtil;
use App\Utils\ResponseUtil;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BannerController extends Controller
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

    public function add()
    {

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
        $this->service->operate($this->request->input('id'));
        return ResponseUtil::toJson();
    }

    /**
     * @param Request $request
     * @return string
     * @throws AppException
     */
    public function upload(Request $request)
    {
        if ($request->isMethod('post')) {
            $file = $request->file('file');
            // 文件是否上传成功
            if ($file->isValid()) {
                // 获取文件相关信息
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                // 上传文件
                $filename = $this->filename(uniqid()) . '.' . $ext;
                $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
                if($bool)
                {
                    $imgUtil = new ImgUtil();
                    $data['url'] = $imgUtil->ossUpload($filename,'banner');
                    return ResponseUtil::toJson($data);
                }
                throw new AppException('上传失败');
            }
            throw new AppException('文件上传出错');
        }
    }

    /**
     * @param $data
     * @return string
     * 获得文件名
     */
    private function filename($data)
    {
        $filename = md5(time() . $data);
        return $filename;
    }
}