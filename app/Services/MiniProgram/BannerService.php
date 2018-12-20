<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 13:24
 */

namespace App\Services\MiniProgram;

use App\Exceptions\AppException;
use App\Models\MiniProgram\Banner;

define('FULL_NOT_PRIZE_NUMBER',3);
define('FULL_PRIZE_NUMBER',1);

class BannerService
{
    /**
     * @param $id
     * 上、下架Banner图
     */
    public function operate($id)
    {
        $record = Banner::get($id);
        if($record['isShow'] == Banner::SHOW)
        {
            $record['isShow'] = Banner::NOT_SHOW;
        }
        else
        {
            $this->checkIsFull($record['isPrize']);
            $record['isShow'] = Banner::SHOW;
        }
        $record->save();
    }

    /**
     * @param $isPrize
     * @throws AppException
     * 检查分类下的banner图是否已经满了
     */
    private function checkIsFull($isPrize)
    {
        $count = Banner::where('isShow',Banner::SHOW)
                       ->where('isPrize',$isPrize)
                       ->count();
        if($isPrize == Banner::PRIZE)
        {
            if($count == FULL_PRIZE_NUMBER)
            {
                throw new AppException('请先下架一个Banner图');
            }
        }
        else
        {
            if($count == FULL_NOT_PRIZE_NUMBER)
            {
                throw new AppException('请先下架一个Banner图');
            }
        }
    }

    public function add($image,$isPrize,$type,$url)
    {
        $this->checkIsFull($isPrize);
        switch ($type)
        {
            case Banner::GOODS_DETAIL:
                $url = '/pages/details/details?id=' . $url;
                break;
            case Banner::ACTIVITY_DETAIL:
                $url = '/pages/activity/details?id=' . $url;
                break;
            case Banner::GOODS_CATEGORY:
                $url = '/pages/juxiaocang-fenlei/juxiaocang-fenlei?item=' . $url;
                break;
        }
        Banner::create([
            'image' => $image,
            'url'   => $url,
            'isPrize' => $isPrize,
            'type' => $type
        ]);
    }
}