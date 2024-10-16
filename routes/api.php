<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AccountController;

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

Route::get('/', function () {
    return response()->json("Genius System " . now(), 200);
});
Route::middleware('auth:api')->get('/auth', function (Request $request) {
    return $request->user();
});
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('request-reset-password', [AuthController::class, 'requestPasswordReset']);
    Route::post('reset-password', [AuthController::class, 'passwordReset']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('me', [AccountController::class, 'me']);
        Route::get('permissions', [AccountController::class, 'permission']);
        Route::post('update-account', [AccountController::class, 'updateAccount']);
        Route::post('update-password', [AccountController::class, 'updatePassword']);
        Route::post('request-new-verify-email', [AccountController::class, 'requestNewVerifyEmail']);
        Route::post('verify-email', [AccountController::class, 'verifyEmail']);
        Route::post('logout', [AccountController::class, 'logout']);
    });
});
