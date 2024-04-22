<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\ProfileController;

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
Route::get('auth/test', [AuthController::class, 'attempt']);
Route::group(['middleware' => ['api']], function () {
    Route::post('auth/attempt', [AuthController::class, 'attempt']);
    // login google
    Route::get('auth/login', [AuthController::class, 'login']);
    Route::get('google/callback', [AuthController::class, 'googleCallback']);
    //user
    Route::post('user/register', [UserController::class, 'register']);
});

Route::group(['middleware' => ['auth:api', 'author']], function () {
    Route::get('auth/logout', [AuthController::class, 'logout']);
    Route::prefix('profile')->group(function () {
        Route::post('update', [ProfileController::class, 'update']);
        Route::put('change-password', [ProfileController::class, 'updatePassword']);
    });
    Route::prefix('manage')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'fetch']);
            Route::get('authenticate', [UserController::class, 'userAuthenticate']);
            Route::post('store', [UserController::class, 'createAdmin']);
            Route::get('{user_id}', [UserController::class, 'findUser']);
            Route::put('{user_id}/update', [UserController::class, 'update']);
            Route::delete('{user_id}/delete', [UserController::class, 'delete']);
            Route::get('{user_id}/permission', [UserController::class, 'fetchPermission']);
            Route::put('{user_id}/permission/update', [UserController::class, 'updatePermission']);
        });
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'fetch']);
            Route::get('{role_id}/permission', [RoleController::class, 'fetchPermission']);
            Route::post('store', [RoleController::class, 'create']);
            Route::put('{role_id}/update', [RoleController::class, 'update']);
            Route::put('{role_id}/permission/update', [RoleController::class, 'updatePermission']);
            Route::delete('{role_id}/delete', [RoleController::class, 'delete']);
        });
        Route::prefix('tools')->group(function () {
            Route::get('/', [ToolController::class, 'fetch']);
            Route::post('store', [ToolController::class, 'create']);
            Route::put('move', [ToolController::class, 'updateMove']);
            Route::put('{tool_id}/update', [ToolController::class, 'update']);
            Route::delete('{tool_id}/destroy', [ToolController::class, 'delete']);
        });
    });
});
