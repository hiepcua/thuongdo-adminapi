<?php

use App\Http\Controllers\TrashController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/search', [TrashController::class, 'search']);
    Route::get('/search-one', [TrashController::class, 'searchOne']);
    Route::post('/clear', [TrashController::class, 'clear']);
    Route::get('/get-history', [TrashController::class, 'getHistory']);
});
