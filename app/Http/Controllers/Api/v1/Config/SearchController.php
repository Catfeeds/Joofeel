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
        $add = [
            'name' => $this->request->input('content')
        ];
        $data = Cache::get('hotSearch');
        array_push($data,$add);
        Cache::forever('hotSearch',$data);
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 删除
     */
    public function delete()
    {
        $this->validate($this->request,
            [
                'content' => 'required|string'
            ]);
        $data = Cache::get('hotSearch');
        foreach ($data as $key => $item)
        {
            if($item['name'] == $this->request->input('content'))
            {
                array_splice($data,$key,1);
            }
        }
        Cache::forever('hotSearch',$data);
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
                'content'        => 'required|string',
                'update_content' => 'required|string'
            ]);
        $data = Cache::get('hotSearch');
        $index = 0;
        foreach ($data as $key =>  $item)
        {
            if($item['name'] == $this->request->input('content'))
            {
                $index = $key;
            }
        }
        $data[$index]['name'] = $this->request->input('update_content');
        Cache::forever('hotSearch',$data);
        return ResponseUtil::toJson($data);
    }
}