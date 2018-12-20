<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/12/20
 * Time: 13:09
 */

namespace App\Http\Controllers\Api\v3;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Models\Enter\Merchants;
use App\Utils\Common;
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
        try{
            $this->checkExistsAccount(
                $this->request->input('account'),
                $this->request->input('phone'));
        }catch (AppException $exception)
        {
            return ResponseUtil::toJson('',$exception->getMessage(),$exception->getCode());
        }
        $this->regMerchants($this->request->all());
        return ResponseUtil::toJson();
    }

    /**
     * @param $account
     * @param $phone
     * @throws AppException
     */
    private function checkExistsAccount($account,$phone)
    {
        $merchantsAccount = Merchants::where('account',$account)->first();
        if($merchantsAccount)
        {
            throw new AppException('该账号已存在');
        }
        $merchantsPhone = Merchants::where('phone',$phone)->first();
        if($merchantsPhone)
        {
            throw new AppException('该账号已存在');
        }
    }

    /**
     * @param $data
     * 注册
     */
    private function regMerchants($data)
    {
        $data['api_token'] = Common::generateToken();
        $data['password'] = md5($data['password']);
        Merchants::create($data);
    }
}