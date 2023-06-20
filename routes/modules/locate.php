<?php

use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ProvinceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/province', [ProvinceController::class, 'index']);
        Route::get('/district/{provinceId}', [DistrictController::class, 'listByProvinceId']);
        Route::get('/ward/{districtId}', [DistrictController::class, 'listByDistrictId']);
    }
);
