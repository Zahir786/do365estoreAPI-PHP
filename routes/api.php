<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckMaintenance;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', ['middleware' => ['maintenance'], 'uses' => 'AuthController@login']);

Route::post('/changePassword', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'SettingsController@changePassword']);
Route::post('/synchronize', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'SettingsController@synchronize']);
Route::post('/logout', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'SettingsController@logout']);

//Category
Route::post('/getCategories', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'CategoryController@list']);
Route::post('/addCategory', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'CategoryController@add']);
Route::post('/updateCategory', ['middleware' => ['maintenance', 'sessionMaintenance'], 'uses' => 'CategoryController@update']);

//Role
Route::post('/getRole', ['middleware' => ['maintenance'], 'uses' => 'RoleController@list']);
Route::post('/addRole', ['middleware' => ['maintenance'], 'uses' => 'RoleController@add']);
Route::post('/updateRole', ['middleware' => ['maintenance'], 'uses' => 'RoleController@update']);
