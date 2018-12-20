<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/11
 * Time: 10:30
 */

namespace App\Http\Controllers\Api\v1\Config;

use App\Http\Controllers\Controller;
use App\Models\MiniProgram\Title;

class ConfigController extends Controller
{
    /**
     * 今日推荐(定时任务)
     */
    public function title()
    {
        $title = Title::where('isShow', 0)
                      ->first();
        $title['isShow'] = 1;
        $title->save();
        if ($title['id'] == 3) {
            $newTitle = Title::where('id',1)->first();
        } else {
            $newId = $title['id'] + 1;
            $newTitle = Title::where('id',$newId)->first();
        }
        $newTitle['isShow'] = 0;
        $newTitle->save();
    }
}