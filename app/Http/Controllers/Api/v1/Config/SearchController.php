<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 15:35
 */

namespace App\Http\Controllers\Api\v1\Config;

use App\Http\Controllers\Controller;
use App\Utils\ResponseUtil;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取
     */
    public function get()
    {
        $data = Cache::get('hotSearch');
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
               'content' => 'required|string'
            ]);
        $data = Cache::get('hotSearch');
        array_push($data,$this->request->input('content'));
        Cache::pull('hotSearch',$data);
        return ResponseUtil::toJson($data);
    }
}