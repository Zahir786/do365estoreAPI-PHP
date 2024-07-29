<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;

class AuthController extends Controller
{
    public function __construct()
    {
        
    }
    
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        try {
            $validationArr = array(
                'username' => 'required|string',
                'password' => 'required|string',
            );
            
            $validator = Validator::make($request->all(), $validationArr, []);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    "action" => "login",
                    "error" => 1,
                    "errorCode" => 104,
                    "message" => $errors->first(),
                    "status" => "failure",
                ]);
            } else {
                $credentials = $request->only('username', 'password');
                
                $adminDetail = Admin::where('username', $credentials['username'])->first();
                if(empty($adminDetail)) {
                    return response()->json([
                        "action" => "login",
                        "error" => 1,
                        "errorCode" => 103,
                        "message" => "Invalid Username",
                        "status" => "failure",
                    ]);
                } else {
                    if (!Hash::check($credentials['password'], $adminDetail->PASSWORD)) {
                        return response()->json([
                            "action" => "login",
                            "error" => 1,
                            "errorCode" => 102,
                            "message" => "Invalid Password",
                            "status" => "failure",
                        ]);
                    } else {
                        if($adminDetail->ACCOUNT_STATUS != 'ACTIVE') {
                            return response()->json([
                                "action" => "login",
                                "error" => 1,
                                "errorCode" => 101,
                                "message" => "Your account is inactive",
                                "status" => "failure",
                            ]);
                        } else {
                            if ($token = JWTAuth::fromUser($adminDetail)) {
                                $tokenArr = explode('.', $token);
                                $tokenHeaderArr = $tokenArr[1];
                                $tokenHeaderJsonArr = base64_decode($tokenHeaderArr);
                                $tokenHeaderDetail = json_decode($tokenHeaderJsonArr, true);
                                
                                Admin::where('ADMIN_ID', $adminDetail->ADMIN_ID)->update([
                                    'LOGIN_STATUS' => 1,
                                    'AUTH_TOKEN' => $token,
                                    'SESSION_ID' => $tokenHeaderDetail['jti'],
                                    'LAST_LOGIN_DATE' => date('Y-m-d H:i:s')
                                ]);

                                return response()->json([
                                    "action" => "login",
                                    "accessToken" => $token,
                                    "error" => 0,
                                    "errorCode" => 100,
                                    "message" => "Logged in",
                                    "status" => "success",
                                ])->withCookie(new Cookie('token', $token));
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "login",
                "error" => 1,
                "errorCode" => 101,
                "message" => $e->getMessage(),
                "status" => "failure",
            ], 500);
        }
    }
}
