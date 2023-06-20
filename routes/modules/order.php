<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [OrderController::class, 'pagination']);
        Route::get('/{id}', [OrderController::class, 'detail']);
        Route::post('/{id}', [OrderController::class, 'update']);
        Route::patch('/change-staff/{order}', [OrderController::class, 'changeStaff']);
        Route::patch('/cancel/{order}', [OrderController::class, 'cancel']);
        Route::patch('/status/{order}', [OrderController::class, 'changeStatus']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
        Route::get('/get-debt/{packageIds}', [OrderController::class, 'getDebt']);
        Route::get('/products/{id}', [OrderController::class, 'getProducts']);
        Route::group(['prefix' => 'note/{orderId}'], function() {
            Route::get('/', [OrderController::class, 'getNotes']);
            Route::post('/', [OrderController::class, 'addNote']);
        });
    }
);