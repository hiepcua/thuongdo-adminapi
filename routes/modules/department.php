<?php

use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [DepartmentController::class, 'pagination']);
        Route::get('/list', [DepartmentController::class, 'index']);
        Route::post('/', [DepartmentController::class, 'store']);
        Route::put('/{deparment}', [DepartmentController::class, 'update']);
        Route::delete('/{deparment}', [DepartmentController::class, 'destroy']);
    }
);