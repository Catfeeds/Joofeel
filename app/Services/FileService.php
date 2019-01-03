<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 15:08
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Utils\ImgUtil;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function upload($request,$url)
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
                    $data['url'] = $imgUtil->ossUpload($filename,'banner',$url);
                    return $data;
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