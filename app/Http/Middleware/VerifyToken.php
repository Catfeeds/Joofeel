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

class VerifyToken extends Controller
{
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
            return $next($request);
        }
        return response()->json(
            [
                'code'=>200,
                'msg'=> '您尚未登录'
            ]);
    }

}