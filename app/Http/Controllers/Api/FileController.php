<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/4
 * Time: 18:23
 */

namespace App\Http\Controllers\Api;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Utils\ImgUtil;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * @param $bucket
     * @param $url
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function upload($bucket,$url)
    {
        if ($this->request->isMethod('post')) {
            $file = $this->request->file('file');
            // 文件是否上传成功
            if ($file) {
                // 获取文件相关信息
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                // 上传文件
                $filename = $this->filename(uniqid()) . '.' . $ext;
                $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));
                if($bool)
                {
                    $imgUtil = new ImgUtil();
                    $src = $imgUtil->ossUpload($filename,$bucket,$url);
                    return $src;
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