<?php
/**
 * Created by PhpStorm.
 * User: locust
 * Date: 2018/10/31
 * Time: 16:41
 */

namespace App\Http\Middleware;

use App\Services\Token\TokenService;
use Closure;

class VerifyToken
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
        if(TokenService::getCurrentTokenVar('token')){
            return $next($request);
        }
    }

}