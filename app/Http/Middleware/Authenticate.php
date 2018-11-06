<?php

namespace App\Http\Middleware;

use App\Exceptions\ExceptionCode;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

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
        $logStatus = $this->auth->check();
        if (!$logStatus) {
            return response()->json(
                [
                    'code'=>ExceptionCode::REDIRECT_TO_LOGIN,
                    'msg'=> '您尚未登录'
                ]);
        }

        return $next($request);
    }
}