<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/13
 * Time: 13:58
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\BaseController;
use App\Models\MiniProgram\Party\PartyLabel;
use App\Services\MiniProgram\PartyService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class PartyController extends BaseController
{
    private $service;

    public function __construct(Request $req,PartyService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    public function addCommunity()
    {
        $this->validate($this->request,
        [
            'description' => 'required|string',
            'details'     => 'required|string',
            'way'         => 'required|string',
            'people_no'   => 'required|integer',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'sign_time'   => 'required',
            'city'        => 'required|string',
            'site'        => 'required|string',
            'image'       => 'required',
        ]);
        $this->service->addCommunity(
            $this->request->input('description'), //
            $this->request->input('details'),     //
            $this->request->input('way'),         //
            $this->request->input('people_no'),
            $this->request->input('start_time'),   //
            $this->request->input('end_time'),   //
            $this->request->input('sign_time'),   //
            $this->request->input('city'),
            $this->request->input('site'),
            $this->request->input('image'),
            $this->request->input('contact'));
        return ResponseUtil::toJson();
    }

    public function addPartyGoods()
    {
        $this->validate($this->request,
            [
                'goods_id' => 'required|integer',
                'party_id' => 'required|integer|exists:mysql.party,id'
            ]);
        try{
            $this->service->addPartyGoods(
                $this->request->input('party_id'),
                $this->request->input('goods_id'));
        }catch (\Exception $ex){
            return ResponseUtil::toJson('',$ex->getMessage(),$ex->getCode());
        }
        return ResponseUtil::toJson();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 上传官方聚会图
     */
    public function uploadCommunity()
    {
        $data['src'] =
            (new FileController($this->request))->upload('jufeeloss','test');
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索社区聚会
     */
    public function searchCommunity()
    {
        $this->validate($this->request,
            [
                'content' => 'required|string'
            ]);
        $data = $this->service->searchCommunity(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索聚会
     */
    public function search()
    {
        $this->validate($this->request,
            [
                'content' => 'required|string'
            ]);
        $data = $this->service->search(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取聚会
     */
    public function getCommunity()
    {
        $data = $this->service->getCommunity(
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取聚会
     */
    public function get()
    {
        $this->validate($this->request,
            [
                'sign' => 'required|integer|min:0'
            ]);
        $data = $this->service->get(
            $this->request->input('limit'),
            $this->request->input('sign'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 聚会详情
     */
    public function detail()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.party,id'
            ]);
        $data = $this->service->detail($this->request->input('id'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 删除评论
     */
    public function deleteMessage()
    {
        $this->validate($this->request,
            [
                'id' => 'required|integer|exists:mysql.message,id'
            ]);
        $this->service->deleteMessage($this->request->input('id'));
        return ResponseUtil::toJson();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * 添加标签
     */
    public function label()
    {
        $this->validate($this->request,
            [
                'id'      => 'required|integer|exists:mysql.party,id',
                'content' => 'required|string|max:10'
            ]);
        $this->service->label(
            $this->request->input('id'),
            $this->request->input('content'));
        return ResponseUtil::toJson();
    }

    public function deleteLabel()
    {
        $this->validate($this->request,
            [
                'id'      => 'required|integer|exists:mysql.party,id',
            ]);
        PartyLabel::where('id',$this->request->input('id'))->delete();
        return ResponseUtil::toJson();
    }
}