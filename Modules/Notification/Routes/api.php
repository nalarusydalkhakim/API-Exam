<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\NotificationTokenController;

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

/* Route::middleware('auth:api')->get('/notification', function (Request $request) {
    return $request->user();
});
 */

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/{notification}/read', [NotificationController::class, 'read']);
    Route::post('notifications/read-all', [NotificationController::class, 'readAll']);

    Route::post('notifications/add-task', [NotificationController::class, 'addCourseTask']);
    Route::post('notifications/add-content', [NotificationController::class, 'addCourseContent']);
    Route::post('notifications/add-announcement', [NotificationController::class, 'addAnnouncement']);
    Route::post('tokens', [NotificationTokenController::class, 'store']);
});
