<?php

use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [OrganizationController::class, 'pagination']);
        Route::get('/list', [OrganizationController::class, 'index']);
        Route::post('/', [OrganizationController::class, 'store']);
        Route::put('/{deparment}', [OrganizationController::class, 'update']);
        Route::delete('/{deparment}', [OrganizationController::class, 'destroy']);
    }
);