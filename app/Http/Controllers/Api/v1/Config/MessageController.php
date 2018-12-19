<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/14
 * Time: 13:52
 */

namespace App\Http\Controllers\Api\v1\Config;

use App\Http\Controllers\BaseController;
use App\Services\Util\MessageService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class MessageController extends BaseController
{
    private $service;
    public function __construct(Request $req,MessageService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     *群发消息
     */
    public function send()
    {
        $this->validate($this->request,
            [
                'theme' => 'required|string',
                'tips' => 'required|string',
                'note' => 'required|string',
            ]);
        $this->service->prepareFormId(
            $this->request->input('theme'),
            $this->request->input('tips'),
            $this->request->input('note'));
        return ResponseUtil::toJson();
    }
}