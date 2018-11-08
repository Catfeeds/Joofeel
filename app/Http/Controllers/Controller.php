<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use ResponseTrait;
    use AuthorizesRequests, DispatchesJobs;
    use ValidatesRequests{
        // 重写 构建自己的验证响应
        buildFailedValidationResponse as parentBuildFailedValidationResponse;
        throwValidationException as parentThrowValidationException;
    }

    protected $request;
    protected $inputs;

    public function __construct(Request $req)
    {
        $this->request = $req;
        $this->inputs = $req->all();
    }

    // 重写了验证抛错的方法
    protected function throwValidationException(Request $request, $validator)
    {
        throw new \Exception(array_last(array_last($this->formatValidationErrors($validator))),10000);
    }

    /**
     * 过滤输入参数
     * @param $input
     * @param null $nameList
     * @return array
     */
    protected function inputFilter($input, $nameList = null)
    {
        $ret = [];
        if ($nameList) {
            foreach ($nameList as $value) {
                //if (isset($input[$value])) {
                if (array_key_exists($value, $input)) {
                    $ret[$value] = $input[$value];
                    if ($ret[$value] === null) {
                        $ret[$value] = '';
                    }
                }
            }
        } else {
            foreach ($input as $key => $value) {
                if ($value === null) {
                    $value = '';
                }
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    /**
     * 获取管理员id
     */
    protected function getAdminId()
    {
        $token = $this->request->input('token');
        $admin = Admin::where('api_token',$token)->first();
        return $admin['id'];
    }

}
