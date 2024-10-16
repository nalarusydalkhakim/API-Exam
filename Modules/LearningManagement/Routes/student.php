<?php

use Illuminate\Support\Facades\Route;
use Modules\LearningManagement\Http\Controllers\Student\EventTaskController;
use Modules\LearningManagement\Http\Controllers\Student\DashboardController;

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
    'prefix' => 'lms/student',
    'as' => 'lms.user.',
    'middleware' => [
        'access_level:user',
    ]
], function () {
    route::get('dashboard', [DashboardController::class, 'getStudentDashboardById'])->name('dashboard.getStudentDashboardById');
    route::post('event-tasks/{event_task}/start', [EventTaskController::class, 'start']);
    route::post('event-tasks/{event_task}/task-questions/{task_question}/answer', [EventTaskController::class, 'answer']);
    route::post('event-tasks/{event_task}/task-questions/{task_question}/set-mark', [EventTaskController::class, 'setMark']);
    route::post('event-tasks/{event_task}/finish', [EventTaskController::class, 'finish']);
    route::apiResource('event-tasks', EventTaskController::class)->only(['index', 'show']);
});
