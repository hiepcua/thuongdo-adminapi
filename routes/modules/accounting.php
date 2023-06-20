<?php

use App\Http\Controllers\AccountingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::patch('/order-fee', [AccountingController::class, 'getOrderFee']);
        Route::patch('/inspection', [AccountingController::class, 'getInspectionCost']);
        Route::patch('/international', [AccountingController::class, 'getInternationalCost']);
    }
);