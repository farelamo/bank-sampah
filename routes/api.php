<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashOutController;
use App\Http\Controllers\GarbageController;
use App\Http\Controllers\GarbageDepositController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\WithdrawController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth.api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::resource('user', UserController::class)->except(['create', 'edit']);
    Route::resource('garbage', GarbageController::class)->except(['create', 'edit']);
    Route::resource('deposit', GarbageDepositController::class)->except(['create', 'edit']);
    Route::get('transaction', [GarbageDepositController::class, 'transaction']);

    Route::get('withdraw', [WithdrawController::class, 'show']);
    Route::put('withdraw-update', [WithdrawController::class, 'update']);

    Route::get('cashout-date', [CashOutController::class, 'showCashOutDate']);
    Route::post('cashout-date', [CashOutController::class, 'cashOutDate']);
    Route::put('update-cashout-date', [CashOutController::class, 'updateCashOutDate']);

    Route::put('photo_trx/{id}', [CashOutController::class, 'photoTrx']);
    Route::get('photo_trx/{id}', [CashOutController::class, 'getPhotoTrx']);

    Route::get('cashout', [CashOutController::class, 'getCashOutMine']);
    Route::get('cashout-admin', [CashOutController::class, 'getCashOut']);

    Route::post('upload_data_trx', [UploadController::class, 'data_trx']);
    Route::post('upload_nasabah', [UploadController::class, 'data_nasabah']);
    Route::post('upload_data_sampah', [UploadController::class, 'data_sampah']);
});
