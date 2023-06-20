<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware('non-auth')->group(
    function () {
        Route::post('/upload', [MediaController::class, 'upload']);
        Route::post('/single', [MediaController::class, 'single']);
    }
);
