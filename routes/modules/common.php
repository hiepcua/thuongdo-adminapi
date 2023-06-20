<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\LabelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/customer', [CommonController::class, 'getCategoriesCustomer']);
        Route::get('/order', [CommonController::class, 'getListCategoriesOrder']);
        Route::get('/fine', [CommonController::class, 'getListCategoriesFine']);
        Route::get('/withdrawal', [CommonController::class, 'getCategoriesWithdrawal']);
        Route::get('/package', [CommonController::class, 'getCategoriesPackages']);
        Route::get('/delivery', [CommonController::class, 'getCategoriesDelivery']);
        Route::get('/complain', [CommonController::class, 'getCategoriesComplain']);
    }
);