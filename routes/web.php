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

Route::get('/verification', [UserWebServerSideController::class, 'verification']);
Route::get('/resetpw', [UserWebServerSideController::class, 'resetPassword']);
Route::post('/change', [UserWebServerSideController::class, 'submitNewPassword']);
Route::get('/error404', [UserWebServerSideController::class, 'error404']);

Route::get('/auth', [CMSAuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [CMSAuthController::class, 'login']);
Route::post('/logout', [CMSAuthController::class, 'logout']);

Route::get('/cms', [CMSController::class, 'index']);
Route::get('/cms/helper', [CMSController::class, 'index']);
Route::get('/cms/bantuan', [CMSController::class, 'index']);
