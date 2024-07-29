<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class Authenticate
{
    protected $auth;
    protected $jwt;
    public function __construct(Auth $auth, JWTAuth $jwt)
    {
        $this->auth = $auth;
        $this->jwt = $jwt;
    }

    public function handle(Request $request, Closure $next, ...$guards)
    {
        // if(!auth()->check()) {
        //     return response()->json([
        //         "action" => "gerenal",
        //         "error" => 0,
        //         "errorCode" => 100,
        //         "message" => "Session is expired, please login to continue",
        //         "status" => "failure",
        //     ], 401);
        // }

        return $next($request);
    }
}