<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/11/8
 * Time: 18:04
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\AdminService;
use App\Utils\ResponseUtil;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $service;

    public function __construct(Request $req,AdminService $service)
    {
        $this->service = $service;
        parent::__construct($req);
    }

    /**
     * 禁止登录/或者取消禁止登录
     */
    public function ban()
    {
        $this->validate(
            $this->request,
            [
                'id' => 'required|integer|exists:mysql.admin,id'
            ]);
        $this->service->ban($this->request->input('id'));
        return ResponseUtil::toJson();
    }

    /**
     * @return mixed
     * 获取所有用户
     */
    public function get()
    {
        $data = Admin::orderByDesc('created_at')
                     ->get();
        return ResponseUtil::toJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 设置权限
     */
    public function set()
    {
        $this->validate(
            $this->request,
            [
                'id'    => 'required|integer|exists:mysql.admin,id',
                'scope' => 'required|integer|in:16,32,64,128'
            ]);
        $this->service->set(
            $this->request->input('id'),
            $this->request->input('scope'));
        return ResponseUtil::toJson();
    }
}