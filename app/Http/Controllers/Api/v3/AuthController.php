<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 13:09
 */

namespace App\Http\Controllers\Api\v3;

use App\Http\Controllers\Controller;
use App\Models\Enter\Merchants;
use App\Utils\ResponseUtil;

class AuthController extends Controller
{
    public function reg()
    {
        $this->validate($this->request,
            [
                'account'        => 'required|string|max:10',
                'password'       => 'required|string|max:20',
                'phone'          => 'required|regex:/^1[3456789][0-9]{9}$/',
                'merchants_name' => 'required|string|max:20'
            ]);
        $this->checkExistsAccount($this->request->input('account'));
        $this->regMerchants($this->request->all());
        return ResponseUtil::toJson();
    }

    private function checkExistsAccount($account)
    {
        $merchants = Merchants::where('account',$account)->first();
        if($merchants)
        {
            return ResponseUtil::toJson('','该账号已存在',300);
        }
    }

    private function regMerchants($data)
    {
        $data['api_token'] = Merchants::create($data);
    }
}