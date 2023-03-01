<?php

use App\Http\Controllers\BantuanController;
use App\Http\Controllers\BantuanOrderController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'update']);
    Route::post('user/avatar', [UserController::class, 'changeAvatar']);
    Route::post('user/logout', [UserController::class, 'logout']);
    Route::post('user/changepw', [UserController::class, 'changePassword']);

    Route::post('helper', [HelperController::class, 'cuanRequest']);
    Route::get('helper', [HelperController::class, 'fetch']);
    Route::get('helper/check', [HelperController::class, 'checkAvailibility']);
    Route::post('helper/update', [HelperController::class, 'update']);
    Route::post('helper/rate', [HelperController::class, 'rateHelper']);
    Route::get('helper/rate', [HelperController::class, 'getAllRate']);

    Route::post('bantuan', [BantuanController::class, 'create']);
    Route::get('bantuan', [BantuanController::class, 'get']);
    Route::post('bantuan/update', [BantuanController::class, 'update']);
    Route::delete('bantuan', [BantuanController::class, 'delete']);
    
    Route::get('category', [BantuanController::class, 'category']);

    Route::post('order', [BantuanOrderController::class, 'create']);
});

Route::post('device', [UserController::class, 'storeUserDeviceId']);
Route::get('device', [UserController::class, 'fetchUserDeviceId']);
Route::post('device/update', [UserController::class, 'updateUserDeviceId']);

Route::post('user/login', [UserController::class, 'login']);
Route::post('user/register', [UserController::class, 'register']);
Route::post('user/resetpw', [UserController::class, 'resetPassword']);