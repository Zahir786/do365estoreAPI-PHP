<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Cookie;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Log;

class CheckSessionMaintenance
{
    protected $auth;
    protected $jwt;
    public function __construct(Auth $auth, JWTAuth $jwt)
    {
        $this->auth = $auth;
        $this->jwt = $jwt;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if(auth()->check()) {
            $token = $request->cookie('authToken');
            //$token = $this->jwt->getToken();
            if($token != '') {
                $tokenArr = explode('.', $token);
                $tokenHeaderArr = $tokenArr[1];
                $tokenHeaderJsonArr = base64_decode($tokenHeaderArr);
                $tokenHeaderDetail = json_decode($tokenHeaderJsonArr, true);

                if($tokenHeaderDetail['sub'] != '') {
                    $getUserDetail = User::where('USER_ID', $tokenHeaderDetail['sub'])->first();
                    if(!empty($getUserDetail)) {
                        if($getUserDetail->SESSION_ID != $tokenHeaderDetail['jti']) {
                            $this->jwt->invalidate($this->jwt->getToken());
            
                            auth()->logout();

                            $responseArr = array(
                                "error" => 1,
                                "errorCode" => 501,
                                "msg" => "Your session ID is invalid.",
                                "status" => "failure"
                            );
                
                            Log::channel('custom_log')->info('Authentication - Your session ID is invalid');
                
                            return response()->json($responseArr)->withCookie(new cookie('token', ''));
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
