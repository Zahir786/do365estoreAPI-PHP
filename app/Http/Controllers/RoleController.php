<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\role;
use Log;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        try {
            $getRole = role::where('status', '1')->orderBy('created_on', 'DESC')->get();

            $responseArr = array(
                "action" => "role",
                "error" => 0,
                "errorCode" => 100,
                "message" => "role Detail",
                "status" => "success",
                "categoryDetail" => $getRole,
            );

            return response()->json($responseArr);
        } catch (\Exception $e) {
            Log::channel('custom_log')->info('Logout Response - ' . json_encode($e->getMessage()));

            return response()->json([
                "action" => "logout",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }

    /**
     * Add Catgeory (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        try {
            $validationArr = array(
                'name' => 'required|string',
                'order' => 'required|string',
                 'link' => 'required|string'
            );

            $messageArr = [];

            $validator = Validator::make($request->all(), $validationArr, $messageArr);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    "action" => "addRole",
                    "error" => 1,
                    "errorCode" => 104,
                    "message" => $errors->first(),
                    "status" => "failure",
                ]);
            } else {
                $authDetail = auth()->user();
                role::insert([
                    'name' => $request->input('name'),
                    'link' => $request->input('link'),
                    'order' => 1,
                    'created_by_id' => $authDetail->ADMIN_ID,
                ]);

                return response()->json([
                    "action" => "addRole",
                    "error" => 0,
                    "errorCode" => 100,
                    "message" => 'Role added successfully',
                    "status" => "success",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "addRole",
                "error" => 1,
                "errorCode" => 505,
                "msg" => $e->getMessage(),
                "status" => "failure",
            ], 500);
        }
    }

    /**
     * Update Catgeory (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {
            $validationArr = array(
                'id' => 'required|string',
                'name' => 'required|string',
                'order' => 'required|string',
                 'link' => 'required|string'
            );

            $messageArr = [];

            $validator = Validator::make($request->all(), $validationArr, $messageArr);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    "action" => "updateRole",
                    "error" => 1,
                    "errorCode" => 104,
                    "message" => $errors->first(),
                    "status" => "failure",
                ]);
            } else {
                role::where('id', $request->input('id'))->update([
                    'name' => $request->input('name'),
                    'link' => $request->input('link'),
                ]);

                return response()->json([
                    "action" => "updateRole",
                    "error" => 0,
                    "errorCode" => 100,
                    "message" => 'Role updated successfully',
                    "status" => "success",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "updateRole",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }
}
