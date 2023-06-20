<?php

use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [PackageController::class, 'pagination']);
        Route::get('/{customerId}/vn', [PackageController::class, 'getListVNByCustomerId']);
        Route::get('/{id}', [PackageController::class, 'detail']);
        Route::get('/get-china-cost/{ids}', [PackageController::class, 'getChinaCost']);
        Route::get('/products/{package}', [PackageController::class, 'getProducts']);
        Route::get('/order/{orderId}', [PackageController::class, 'getListByOrderId']);
        Route::post('/order/{order}', [PackageController::class, 'storeByOrderId']);
        Route::post('/customer', [PackageController::class, 'store']);
        Route::put('/{package}', [PackageController::class, 'updateByOrderId']);
        Route::patch('/status', [PackageController::class, 'changeStatus']);
        Route::patch('/{package}', [PackageController::class, 'modifies']);
        Route::delete('/{id}', [PackageController::class, 'destroy']);
    }
);