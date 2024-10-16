<?php

use App\Http\Middleware\AccessLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Modules\Dashboard\Http\Controllers\Committee\DashboardController as CommitteeDashboardController;
use Modules\Dashboard\Http\Controllers\Student\DashboardController;

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

Route::middleware('auth:api')->get('/dashboard', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => [
        'auth:api',
        'access_level:admin'
    ],
    'as' => 'dashboard.admin.',
    'prefix' => 'admin'
], function () {

    Route::get('', [AdminDashboardController::class, 'index']);
});

Route::group([
    'middleware' => [
        'auth:api',
        'access_level:committee'
    ],
    'as' => 'dashboard.committee.',
    'prefix' => 'committee'
], function () {

    Route::get('', [CommitteeDashboardController::class, 'index']);
});


Route::group([
    'middleware' => [
        'auth:api',
        'access_level:user'
    ],
    'as' => 'dashboard.user.',
    'prefix' => 'student'
], function () {

    Route::get('', [DashboardController::class, 'index']);
});
