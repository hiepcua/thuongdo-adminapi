<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/', [PermissionController::class, 'pagination']);
        Route::get('/list', [PermissionController::class, 'index']);
        Route::get('/modules', [PermissionController::class, 'getModulePermission']);
        Route::post('/', [PermissionController::class, 'store']);
        Route::put('/{permission}', [PermissionController::class, 'update']);
        Route::delete('/{permission}', [PermissionController::class, 'destroy']);
    }
);