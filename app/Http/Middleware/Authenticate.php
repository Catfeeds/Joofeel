<?php

namespace App\Http\Middleware;

use App\Exceptions\ExceptionCode;
use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;



    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $admin = Auth::user();
        if ($admin) {
            return $next($request);
        }
        return response()->json(
            [
                'code'=>ExceptionCode::REDIRECT_TO_LOGIN,
                'msg'=> '您尚未登录'
            ]);

    }
}