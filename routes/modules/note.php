<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {

        Route::group(['prefix' => 'order/{id}'], function() {
            Route::get('/public', [NoteController::class, 'getOrderPublic']);
            Route::get('/private', [NoteController::class, 'getOrderPrivate']);
            Route::post('/', [NoteController::class, 'storeOrder']);
        });

        Route::group(['prefix' => 'order-detail/{id}'], function() {
            Route::get('/', [NoteController::class, 'getOrderDetail']);
            Route::post('/', [NoteController::class, 'storeOrderDetail']);
        });


    }
);