<?php

use App\Http\Controllers\CustomerDeliveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [CustomerDeliveryController::class, 'pagination']);
        Route::get('/list', [CustomerDeliveryController::class, 'index']);
        Route::post('/', [CustomerDeliveryController::class, 'store']);
        Route::put('/{customer_delivery}', [CustomerDeliveryController::class, 'update']);
        Route::delete('/{customer_delivery}', [CustomerDeliveryController::class, 'destroy']);
        Route::patch('/status/{customer_delivery}', [CustomerDeliveryController::class, 'changeStatus']);
    }
);
