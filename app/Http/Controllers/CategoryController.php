<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash; 
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\Categories;
use Log;

class CategoryController extends Controller
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
            $getCategories = Categories::select('CATEGORY_ID AS categoryId', 'CATEGORY_NAME AS categoryName')->where('STATUS', 'ACTIVE')->orderBy('CREATED_DATE', 'DESC')->get();
            
            $responseArr = array(
                "action" => "category",
                "error" => 0,
                "errorCode" => 100,
                "message" => "category Detail",
                "status" => "success",
                "categoryDetail" => $getCategories,
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
                'categoryName' => 'required|string',
                'description' => 'required|string'
            );

            $messageArr = [];

            $validator = Validator::make($request->all(), $validationArr, $messageArr);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    "action" => "addCategory",
                    "error" => 1,
                    "errorCode" => 104,
                    "message" => $errors->first(),
                    "status" => "failure",
                ]);
            } else {
                $authDetail = auth()->user();
                Categories::insert([
                    'CATEGORY_NAME' => $request->input('categoryName'),
                    'DESCRIPTION' => $request->input('description'),
                    'CATEGORY_ORDER' => 1,
                    'CREATED_BY' => $authDetail->ADMIN_ID,
                    'CREATED_DATE' => date('Y-m-d H:i:s', strtotime('now'))
                ]);

                return response()->json([
                    "action" => "addCategory",
                    "error" => 0,
                    "errorCode" => 100,
                    "message" => 'Category added successfully',
                    "status" => "failure",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "addCategory",
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
                'categoryId' => 'required|string',
                'categoryName' => 'required|string',
                'categoryDescription' => 'required|string'
            );

            $messageArr = [];

            $validator = Validator::make($request->all(), $validationArr, $messageArr);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json([
                    "action" => "updateCategory",
                    "error" => 1,
                    "errorCode" => 104,
                    "message" => $errors->first(),
                    "status" => "failure",
                ]);
            } else {
                Categories::where('CATEGORY_ID', $request->input('categoryId'))->update([
                    'CATEGORY_NAME' => $request->input('categoryName'),
                    'DESCRIPTION' => $request->input('description'),
                ]);

                return response()->json([
                    "action" => "updateCategory",
                    "error" => 0,
                    "errorCode" => 100,
                    "message" => 'Category updated successfully',
                    "status" => "failure",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "action" => "updateCategory",
                "error" => 1,
                "errorCode" => 505,
                "msg" => "The Server Encountered a Internal Error",
                "status" => "failure",
            ], 500);
        }
    }
}