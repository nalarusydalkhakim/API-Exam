<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::post('oauth/token', [AccessTokenController::class,'issueToken']);
Auth::routes(['register' => false]);

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('home');
})->middleware('auth');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
