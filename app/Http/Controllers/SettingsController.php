<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;    
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Log;

class SettingsController extends Controller
{
    protected $jwt;
    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->middleware('auth:api');
    }
    
    /**
     * Sync
     *
     * @param  Request  $request
     * @return Response
     */
    public function synchronize(Request $request)
    {
        try {
            return response()->json([
                "action" => "synchronize",
                "error" => 0,
                "errorCode" => 100,
                "message" => "Synchronize",
                "status" => "success",
                "time" => date("Y-m-d H:i:s", strtotime('now')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "action" => "synchronize",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }
    
    /**
     * Change Password Update.
     *
     * @param  Request  $request
     * @return Response
     */
    public function changePassword(Request $request)
    {
        try {
            $validationArr = array(
                'currentPassword' => 'required|string',
                'newPassword' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/|different:currentPassword',
                'confirmPassword' => 'required|string|required_with:newPassword|same:newPassword',
            );

            $messageArr = [
                'newPassword.min' => 'The new password must be at least :min characters',
                'newPassword.regex' => 'The new password must contain at least one lowercase letter, one uppercase letter, one number, and one special character (@,$,!,%,*,?,&)',
            ];

            $validator = Validator::make($request->all(), $validationArr, $messageArr);
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
                $authDetail = auth()->user();
                if(!Hash::check($request->input('currentPassword'), $user->PASSWORD)) {
                    return response()->json([
                        "action" => "login",
                        "error" => 1,
                        "errorCode" => 202,
                        "message" => 'Current password is incorrect',
                        "status" => "failure",
                    ]);
                } else {
                    Admin::where('ADMIN_ID', $authDetail->ADMIN_ID)->update([
                        'PASSWORD' => Hash::make($request->input('newPassword')),
                        'PASSWORD_UPDATE' => 1
                    ]);

                    return response()->json([
                        "action" => "login",
                        "error" => 0,
                        "errorCode" => 100,
                        "message" => "Password updated successfully",
                        "status" => "success",
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "changePassword",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $authUser = auth()->user();
            auth()->logout();

            Admin::where('ADMIN_ID', $authUser->ADMIN_ID)->update([
                'LOGIN_STATUS' => 0,
                'SESSION_ID' => ''
            ]);

            return response()->json([
                "action" => "login",
                "error" => 1,
                "errorCode" => 110,
                "message" => "User logged out",
                "status" => "failure",
            ])->withCookie(new cookie('token', ''));
        } catch (\Exception $e) {

            return response()->json([
                "action" => "logout",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }
}