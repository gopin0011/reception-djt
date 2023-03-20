<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function(){
    //AUTH
    Route::post('register', [ApiController::class, 'register']);
    Route::post('login', [ApiController::class, 'login']);
    Route::put('token/{id}', [ApiController::class, 'updatetoken'])->middleware('auth:sanctum');
    Route::get('logout', [ApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('forgot-password',[ApiController::class,'forgotPassword']);

    //ACCOUNT
    Route::get('profil/{id}', [ApiController::class, 'profil'])->middleware('auth:sanctum');
    Route::post('password/{id}', [ApiController::class, 'passwordupdate'])->middleware('auth:sanctum');
    Route::put('profil/{id}', [ApiController::class, 'profilupdate'])->middleware('auth:sanctum');

    //GUEST
    Route::get('dashboard', [ApiController::class, 'dashboard'])->middleware('auth:sanctum');
    Route::get('gates', [ApiController::class, 'gates'])->middleware('auth:sanctum');
    Route::get('rooms', [ApiController::class, 'rooms'])->middleware('auth:sanctum');
    Route::post('store', [ApiController::class, 'store'])->middleware('auth:sanctum');
    Route::post('upload', [ApiController::class, 'upload'])->middleware('auth:sanctum');
    Route::put('update/{id}', [ApiController::class, 'update'])->middleware('auth:sanctum');

    //LIST
    Route::get('booking',[ApiController::class, 'appBooking'])->middleware('auth:sanctum');
    Route::get('all',[ApiController::class, 'appAll'])->middleware('auth:sanctum');
    Route::get('today',[ApiController::class, 'appToday'])->middleware('auth:sanctum');
    Route::get('month',[ApiController::class, 'appMonth'])->middleware('auth:sanctum');
    Route::get('now',[ApiController::class, 'appNow'])->middleware('auth:sanctum');
    Route::get('waiting',[ApiController::class, 'appWaiting'])->middleware('auth:sanctum');
    Route::get('noroom',[ApiController::class, 'appNoRoom'])->middleware('auth:sanctum');

    //DETAIL
    Route::get('detail/{id}',[ApiController::class, 'appDetail'])->middleware('auth:sanctum');

    //UPDATE DETAIL
    Route::put('pulang/{id}',[ApiController::class, 'appPulang'])->middleware('auth:sanctum');
    Route::put('approve/{id}',[ApiController::class, 'appApprove'])->middleware('auth:sanctum');
    Route::put('ruangan/{id}',[ApiController::class, 'appRuangan'])->middleware('auth:sanctum');
});
