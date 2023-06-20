<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/ceo', [ReportController::class, 'getCEO']);
        Route::get('/ceo/warehouse/{id}', [ReportController::class, 'getWarehouse']);
        Route::get('/ceo/category/{type}', [ReportController::class, 'getCategories']);
    }
);