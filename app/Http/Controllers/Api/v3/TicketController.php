<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 16:36
 */

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Enter\Ticket;
use App\Utils\ResponseUtil;

class TicketController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     * 获取所有
     */
    public function get()
    {
        $data = Ticket::get($this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 搜索票
     */
    public function search()
    {
        $this->validate($this->request,
            [
               'content' => 'required|string'
            ]);
        $data = Ticket::search(
            $this->request->input('content'),
            $this->request->input('limit'));
        return ResponseUtil::toJson($data);
    }
}