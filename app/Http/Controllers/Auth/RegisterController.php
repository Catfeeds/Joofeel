<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\ExceptionCode;
use App\Models\Admin;
use App\Models\UserOrg\Employee;
use App\Models\Verify\VerifyMobile;
use App\Services\Traits\PhoneVerify;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function register(Request $request)
    {
        $this->validator($request->all());

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return response()->json([
            'code' => 0,
            'msg' => '注册成功',
            'data' => []
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws \Exception
     */
    protected function validator(array $data)
    {
        $validate = Validator::make($data, [
            'nickname'   => 'required|max:255',
            'account'    => 'required',
            'password'   => 'required|min:6|max:20|confirmed',
        ]);
        if ($validate->fails()){
            throw new \Exception($validate->errors()->first(), 10000);
        }
        // 验证是否已经注册
        $admin = Admin::judgeRegistered($data['account']);
        if (!empty($admin)){
            throw new \Exception('您已经注册，请直接登陆！', ExceptionCode::REDIRECT_TO_LOGIN);
        }

        return $validate;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     * @throws \Exception
     */
    protected function create(array $data)
    {
        $user = Admin::create([
            'account'  => $data['account'],
            'nickname' => $data['nickname'],
            'avatar'   => '',
            'password' => md5($data['password']),
        ]);

        return $user;
    }
}
