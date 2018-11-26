<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 16:41
 */

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Closure;

define('LOGOUT',1001); //登陆失效

class VerifyToken extends Controller
{
    const LOGOUT = 1001;
    /**
     * 处理传入的请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $this->request->input('token');
        $admin = Admin::where('api_token',$token)
                      ->first();
        if($admin)
        {
            if($admin['isBaned'] == Admin::BANED)
            {
                return response()->json(
                    [
                        'code' => LOGOUT,
                        'msg'  => '你已被禁止登陆'
                    ]);
            }
            if(time() - $admin['login_time'] > 7200)
            {
                return response()->json(
                    [
                        'code' => LOGOUT,
                        'msg'  => '登录已失效'
                    ]);
            }
            return $next($request);
        }
        return response()->json(
            [
                'code' => LOGOUT,
                'msg'  => '您尚未登录'
            ]);
    }

}