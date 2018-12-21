<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 16:36
 */

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Enter\TicketOrder;
use App\Services\Enter\UserService;
use App\Utils\ResponseUtil;

class TicketOrderController extends Controller
{
    public function get()
    {
        $data = TicketOrder::getOrder($this->request->input('limit'));
        foreach ($data as $key => $item)
        {
            $data[$key] = UserService::getUser($item);
        }
        return ResponseUtil::toJson($data);
    }

    public function search()
    {
        $this->validate($this->request,
            [
                'content' => 'required|string'
            ]);
        $data = TicketOrder::search(
            $this->request->input('content'),
            $this->request->input('limit'));
        foreach ($data as $key => $item)
        {
            $data[$key] = UserService::getUser($item);
        }
        return ResponseUtil::toJson($data);
    }
}