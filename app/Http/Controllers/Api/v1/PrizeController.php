<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/7
 * Time: 19:06
 */

namespace App\Http\Controllers\Api\v1;


use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Services\PrizeService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class PrizeController extends Controller
{
    private $service;

    public function __construct(Request $req,PrizeService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 抽奖
     */
    public function prize()
    {
        $this->validate(
            $this->request,
            [
                'id'              => 'required|integer|exists:mysql.goods,id',
                'open_prize_time' => 'required|date'
            ]);
        try{
            $data = $this->service->prize(
                $this->request->input('id'),
                $this->request->input('open_prize_time')
            );
        }
        catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        return ResponseUtil::toJson($data);
    }
}