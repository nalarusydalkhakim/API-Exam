<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Event\Http\Controllers\Admin\EventAdminController as AdminEventAdminController;
use Modules\Event\Http\Controllers\Admin\EventParticipantController as AdminEventParticipantController;
use Modules\Event\Http\Controllers\Admin\EventController as AdminEventController;
use Modules\Event\Http\Controllers\Admin\BannerController as AdminBannerController;
use Modules\Event\Http\Controllers\Admin\EventPaymentController as AdminEventPaymentController;
use Modules\Event\Http\Controllers\Admin\EventSponsorController as AdminEventSponsorController;
use Modules\Event\Http\Controllers\User\BannerController;
use Modules\Event\Http\Controllers\User\EventController as UserEventController;
use Modules\Event\Http\Controllers\User\EventParticipantController;
use Modules\Event\Http\Controllers\User\EventPaymentController as UserEventPaymentController;

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
    'middleware' => 'auth:api',
    'prefix' => 'admin',
    'as' => 'event.admin.'
], function () {
    Route::post('banners/{banner}/switch', [AdminBannerController::class, 'swithBannerSequence']);
    Route::apiResource('banners', AdminBannerController::class);
    Route::apiResource('events', AdminEventController::class);
    Route::apiResource('events.event-sponsors', AdminEventSponsorController::class)->shallow();
    Route::apiResource('events.event-admins', AdminEventAdminController::class)->shallow();
    Route::apiResource('events.event-participants', AdminEventParticipantController::class)->shallow();
    Route::apiResource('event-payments', AdminEventPaymentController::class);
    Route::post('event-payments/{eventPayment}/approve', [AdminEventPaymentController::class, 'approve']);
    Route::post('event-payments/{eventPayment}/cancel', [AdminEventPaymentController::class, 'cancel']);
});

Route::group([
    'middleware' => [
        'auth:api',
        'role:Peserta Event'
    ],
    'as' => 'event.user.'
], function () {
    Route::apiResource('banners', BannerController::class);
    Route::apiResource('events', UserEventController::class);
    Route::apiResource('events.event-participants', EventParticipantController::class)->only('index', 'show')->shallow();
    Route::apiResource('event-payments', UserEventPaymentController::class);
    Route::post('event-payments/{eventPayment}/pay', [UserEventPaymentController::class, 'pay']);
    Route::post('event-payments/{eventPayment}/cancel', [UserEventPaymentController::class, 'cancel']);
});


Route::post('event-payments/notifications', [UserEventPaymentController::class, 'notifications']);
