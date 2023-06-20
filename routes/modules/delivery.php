<?php

use App\Http\Controllers\DeliveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [DeliveryController::class, 'pagination']);
        Route::get('/{id}', [DeliveryController::class, 'detail']);
        Route::post('/', [DeliveryController::class, 'store']);
        Route::put('/{id}', [DeliveryController::class, 'update']);
        Route::patch('/{delivery}', [DeliveryController::class, 'modifies']);
        Route::delete('/{id}', [DeliveryController::class, 'destroy']);

        Route::group(
            ['prefix' => 'print/{delivery}'],
            function () {
                Route::get('', [DeliveryController::class, 'printDelivery']);
                Route::get('/ex-warehouse', [DeliveryController::class, 'printExWarehouse']);
                Route::get('/xlsx', [DeliveryController::class, 'getXlsx']);
                Route::patch('/note', [DeliveryController::class, 'storeNote']);
            }
        );
    }
);