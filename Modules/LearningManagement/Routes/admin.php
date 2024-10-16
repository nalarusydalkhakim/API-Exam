<?php

use Illuminate\Support\Facades\Route;
use Modules\LearningManagement\Http\Controllers\Admin\DashboardController;
use Modules\LearningManagement\Http\Controllers\Admin\SubjectController;

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

Route::group([
    'prefix' => 'lms/admin',
    'as' => 'lms.admin.',
    'middleware' => ['access_level:admin'],
], function () {
    route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    route::apiResource('subjects', SubjectController::class);
});
