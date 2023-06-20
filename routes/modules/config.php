<?php

use App\Http\Controllers\ConfigController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/list', [ConfigController::class, 'index']);
    }
);
