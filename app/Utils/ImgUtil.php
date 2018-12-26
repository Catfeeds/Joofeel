<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/4
 * Time: 18:45
 */

namespace App\Utils;

use OSS\Core\OssException;
use OSS\OssClient;

require '../vendor/oss/autoload.php';

class ImgUtil
{
    private $accessKeyId = 'LTAIjfhhjAEa69tU';
    private $accessKeySecret = 'z9jMoqELKVfFwzJUtJVsh304Cwq1LD';
    private $endpoint = 'http://oss-cn-hangzhou.aliyuncs.com';
    private $url = 'https://oss.joofeel.com/';

    /**
     * @param $fileName
     * @param $bucket
     * @param $url
     * @return string
     */
    public function ossUpload($fileName,$bucket,$url){
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
        if(!$ossClient->doesBucketExist($bucket)){
            $ossClient->createBucket($bucket);
        }
        $object = $url. '/' . $fileName;//想要保存文件的名称
        //找到文件在服务器上的根目录
        $path = base_path('public/uploads/' . $fileName);
        try{
            //将文件上传到OSS
            $ossClient->uploadFile($bucket,$object,$path);
            //删除服务器上的文件
            unlink($path);
        } catch(OssException $e) {
            printf($e->getMessage() . "\n");
        }
        return $this->url. $url. '/' . $fileName;
    }
}