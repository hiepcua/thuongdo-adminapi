<?php

use App\Http\Controllers\StatisticController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/fast', [StatisticController::class, 'fast']);
    Route::get('/slow', [StatisticController::class, 'slow']);
});
