<?php

use App\Http\Controllers\CMSAuthController;
use App\Http\Controllers\CMSController;
use App\Http\Controllers\UserWebServerSideController;
use Illuminate\Support\Facades\Route;

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

Route::get('verification', [UserWebServerSideController::class, 'verification']);
Route::get('resetpw', [UserWebServerSideController::class, 'resetPassword']);
Route::post('change', [UserWebServerSideController::class, 'submitNewPassword']);
Route::get('error404', [UserWebServerSideController::class, 'error404']);

Route::get('auth', [CMSAuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('login', [CMSAuthController::class, 'login']);
Route::post('logout', [CMSAuthController::class, 'logout']);

Route::get('cms', [CMSController::class, 'index']);
Route::get('cms/helper', [CMSController::class, 'helper']);
Route::get('cms/bantuan', [CMSController::class, 'bantuin']);
Route::get('cms/notif', [CMSController::class, 'notif']);
Route::post('cms/notif/create', [CMSController::class, 'createNotif']);
Route::post('cms/notif/delete/{id}', [CMSController::class, 'deleteNotif']);
Route::post('cms/helper/accept', [CMSController::class, 'accept']);
Route::post('cms/helper/activate', [CMSController::class, 'activate']);
Route::post('cms/helper/deny', [CMSController::class, 'deny']);
Route::post('cms/helper/stop', [CMSController::class, 'stop']);

Route::get('test', [UserWebServerSideController::class, 'testmail']);
