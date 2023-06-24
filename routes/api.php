<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GarbageController;
use App\Http\Controllers\CashOutController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\GarbageDepositController;

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth.api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::resource('user', UserController::class)->except(['create', 'edit']);
    Route::resource('garbage', GarbageController::class)->except(['create', 'edit']);
    Route::resource('deposit', GarbageDepositController::class)->except(['create', 'edit']);
    Route::get('transaction', [GarbageDepositController::class, 'transaction']);

    Route::resource('withdraw', WithdrawController::class)->only(['show', 'update']);

    Route::get('cashout-date', [CashOutController::class, 'showCashOutDate']);
    Route::post('cashout-date', [CashOutController::class, 'cashOutDate']);
    Route::put('update-cashout-date', [CashOutController::class, 'updateCashOutDate']);

    Route::put('photo_trx/{id}', [CashOutController::class, 'photoTrx']);
    Route::get('photo_trx/{id}', [CashOutController::class, 'getPhotoTrx']);

    Route::get('cashout', [CashOutController::class, 'getCashOutMine']);
    Route::get('cashout-admin', [CashOutController::class, 'getCashOut']);
});
