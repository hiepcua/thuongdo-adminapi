<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/me', [UserController::class, 'me']);
    
        Route::get('/', [UserController::class, 'pagination']);
        Route::get('/list', [UserController::class, 'index']);
        Route::get('/active/{user}', [UserController::class, 'activeUser']);
        Route::post('/', [UserController::class, 'store']);
        Route::post('/avatar', [UserController::class, 'uploadAvt']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::patch('/change-password', [UserController::class, 'changePassword']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    }
);