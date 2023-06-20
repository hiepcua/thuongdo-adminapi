<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::middleware('non-auth')->group(
    function () {
        Route::get('/{attachment}', [MediaController::class, 'getFileId']);
        Route::post('/uploads', [MediaController::class, 'uploads']);
        Route::post('/upload', [MediaController::class, 'singleFile']);
    }
);
