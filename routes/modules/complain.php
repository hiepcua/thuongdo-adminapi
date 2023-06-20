<?php

use App\Http\Controllers\ComplainController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [ComplainController::class, 'pagination']);
        Route::get('/{id}', [ComplainController::class, 'detail']);
        Route::patch('/{complain}', [ComplainController::class, 'modifies']);
        Route::group(['prefix' => 'comment/{id}'], function() {
            Route::get('/{type}', [ComplainController::class, 'getFeedback']);
            Route::get('/{type}/seen', [ComplainController::class, 'seen']);
            Route::post('/', [ComplainController::class, 'storeFeedback']);
        });
    }
);
