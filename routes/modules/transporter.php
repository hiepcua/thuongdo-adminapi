<?php

use App\Http\Controllers\TransporterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [TransporterController::class, 'index']);

        // Nhà Xe
        Route::group(
            ['prefix' => 'children'],
            function () {
                Route::get('/{id}', [TransporterController::class, 'getChildren']);
                Route::post('/{id}', [TransporterController::class, 'storeChildren']);
                Route::put('/{detailId}', [TransporterController::class, 'updateChildren']);
                Route::delete('/{detailId}', [TransporterController::class, 'destroyChildren']);
            }
        );
    }
);