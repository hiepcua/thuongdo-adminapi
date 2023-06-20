<?php

use App\Http\Controllers\FineController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::group(['prefix' => 'withdrawal'], function () {
            Route::get('/', [TransactionController::class, 'getWithdrawal']);
            Route::patch('/status/{withdrawal}', [TransactionController::class, 'withdrawalChangeStatus']);
            Route::get('/{withdrawal}', [TransactionController::class, 'getWithdrawalDetail']);
        });
    }
);