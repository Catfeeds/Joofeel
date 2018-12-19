<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/7
 * Time: 18:40
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\IndexService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

/**
 * Class IndexController
 * @package App\Http\Controllers\Api\v1
 * 首页逻辑业务
 */
class IndexController extends BaseController
{
    private $service;

    public function __construct(Request $req,IndexService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取图表最近7天的用户个数
     */
    public function recentUser()
    {
        $data = $this->service->recentUser();
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取各分类销售占比
     */
    public function salePercent()
    {
        $data = $this->service->salePercent();
        return ResponseUtil::toJson($data);
    }

    /**
     *首页获取全部待办事项
     */
    public function todo()
    {
        $data = $this->service->todo();
        return ResponseUtil::toJson($data);
    }
}