<?php

use App\Http\Controllers\FineController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {

        Route::group(['prefix' => 'comment/{id}'], function() {
            Route::get('/', [FineController::class, 'getComments']);
            Route::post('/', [FineController::class, 'addComment']);
        });

        Route::get('/', [FineController::class, 'pagination']);
        Route::get('/{id}', [FineController::class, 'detail']);
        Route::post('/', [FineController::class, 'store']);
        Route::put('/{id}', [FineController::class, 'update']);
        Route::get('/cancel/{fine}', [FineController::class, 'cancel']);
        Route::patch('/status/{fine}', [FineController::class, 'changeStatus']);
        Route::delete('/{id}', [FineController::class, 'destroy']);
    }
);