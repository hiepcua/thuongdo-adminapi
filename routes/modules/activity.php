<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/order_log/{orderId}', [ActivityController::class, 'getOrderLog']);
        Route::get('/package/{packageId}', [ActivityController::class, 'getPackageLog']);
        Route::get('/package-detail/{packageId}', [ActivityController::class, 'getPackageDetailLog']);
        Route::get('/consignment/{consignment}', [ActivityController::class, 'getConsignmentLog']);
        Route::get('/delivery/{delivery}', [ActivityController::class, 'getDeliveryLog']);
    }
);