<?php

use Illuminate\Support\Facades\Route;
use Modules\LearningManagement\Http\Controllers\School\ClassJournalAttendanceController;
use Modules\LearningManagement\Http\Controllers\School\ClassJournalController;
use Modules\LearningManagement\Http\Controllers\School\EventTaskController;
use Modules\LearningManagement\Http\Controllers\School\DashboardController;
use Modules\LearningManagement\Http\Controllers\School\FileController;
use Modules\LearningManagement\Http\Controllers\School\QuestionController;
use Modules\LearningManagement\Http\Controllers\School\QuestionOptionController;
use Modules\LearningManagement\Http\Controllers\School\TaskController;
use Modules\LearningManagement\Http\Controllers\School\TaskQuestionController;
use Modules\LearningManagement\Http\Controllers\School\TaskResultController;
use Modules\LearningManagement\Http\Controllers\School\TaskSectionController;

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
    'prefix' => 'lms',
    'as' => 'lms.admin-event.',
    'middleware' => [
        'access_level:committee,admin'
    ]
], function () {
    route::get('dashboard', [DashboardController::class, 'getSchoolDashboardById'])->name('dashboard');

    //TASK BANK
    route::apiResource('tasks', TaskController::class);

    //ATTACH DETACH TASK TO EVENT
    route::group(['prefix' => 'tasks/{task}'], function () {
        route::post('attach-to-events', [EventTaskController::class, 'attachTaskToEvents']);
        route::post('event-tasks/{event_task}/detach', [EventTaskController::class, 'detachTaskFromEvent']);
    });
    //TASK EVENTS
    route::group(['prefix' => 'events/{event}/events-tasks'], function () {
        route::post('attach', [EventTaskController::class, 'attachTasksToEvent']);
        route::post('{event_task}/detach', [EventTaskController::class, 'detachTaskFromEvent']);
        //EVENT TASK RESULTS
        route::apiResource('tasks-results', TaskResultController::class);
    });

    //EVENT TASK
    route::apiResource('event-tasks', EventTaskController::class);

    //QUESTION BANK
    route::apiResource('questions', QuestionController::class);
    route::apiResource('questions.options', QuestionOptionController::class)->only('store', 'update', 'destroy');
    //ATTACH QUSTION TO TASK
    route::group(['prefix' => 'tasks/{task}/task-questions'], function () {
        route::post('', [TaskQuestionController::class, 'storeBulk']);
        route::post('attach', [TaskQuestionController::class, 'attach']);
        route::post('bulk-attach', [TaskQuestionController::class, 'bulkAttach']);
        route::post('{task_question}/detach', [TaskQuestionController::class, 'detach']);
    });
    route::apiResource('tasks.task-questions', TaskQuestionController::class)->except('store');
    route::apiResource('tasks.task-sections', TaskSectionController::class);
    route::apiResource('tasks.task-sections.task-questions', TaskQuestionController::class)->only('store');

    //EVENT TASK RESULTS
    route::get('events/{event}/event-task-results', [TaskResultController::class, 'getAllEventTaskResult']);
    route::get('events/{event}/event-task-results/{event_task}', [TaskResultController::class, 'index']);
    route::group(['prefix' => 'task-results'], function () {
        route::get('{task_result}', [TaskResultController::class, 'show']);
        route::post('{task_result}', [TaskResultController::class, 'update']);
        route::get('{task_result}/answers', [TaskResultController::class, 'getAnswer']);
        route::post('{task_result}/question-answer-corrections', [TaskResultController::class, 'answerCorrection']);
        route::post('{task_result}/correction', [TaskResultController::class, 'resultCorrection']);
        route::post('{task_result}/make-chance', [TaskResultController::class, 'makeChance']);
        route::delete('{task_result}', [TaskResultController::class, 'destroy']);
    });

    route::post('files', [FileController::class, 'store']);
});
