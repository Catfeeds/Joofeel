<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/10
 * Time: 13:24
 */

namespace App\Services;

use App\Exceptions\AppException;
use App\Models\Banner;

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
}