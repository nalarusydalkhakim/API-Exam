<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\LearningManagement\Http\Controllers\General\SubjectController;

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

Route::middleware('auth:api')->get('/learningmanagement', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'lms',
    'as' => 'lms.general.'
], function () {
    //SUBJECT
    route::apiResource('subjects', SubjectController::class);
});