<?php

use App\Http\Controllers\CarrierController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::patch('/get-price', [CarrierController::class, 'getPrice']);
    }
);
