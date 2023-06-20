<?php

use App\Http\Controllers\FundTypePayController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/',[FundTypePayController::class, 'pagination'])->name('fund.pagination');
    Route::post('/', [FundTypePayController::class, 'store']);
    Route::put('/{id}', [FundTypePayController::class, 'update']);
    Route::delete('/{id}', [FundTypePayController::class, 'destroy']);

    Route::post('/update-status/{id}', [FundTypePayController::class, 'updateStatus']);

    Route::post('/init-default/', [FundTypePayController::class, 'initDefault']);
});
