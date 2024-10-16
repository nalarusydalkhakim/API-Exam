<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\Admin\UserController as AdminUserController;
use Modules\UserManagement\Http\Controllers\General\RoleController;
use Modules\UserManagement\Http\Controllers\General\UserController;

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

Route::group(['middleware' => 'auth:api'], function () {
    Route::apiResource('users', UserController::class)->only('index', 'show');
    Route::apiResource('roles', RoleController::class)->only('index');
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::apiResource('users', AdminUserController::class);
    });
});
