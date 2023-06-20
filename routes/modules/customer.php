<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(
    function () {
        Route::patch('/warehouse', [CustomerController::class, 'setWarehouse']);
        Route::get('/', [CustomerController::class, 'pagination']);
        Route::get('/reports', [CustomerController::class, 'reports']);
        Route::get('/{customer}', [CustomerController::class, 'detail']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::patch('/change-staff-to-customers', [CustomerController::class, 'changeStaff']);
        Route::patch('/{customer}', [CustomerController::class, 'updateSomething']);
        Route::patch('/status/{customer}', [CustomerController::class, 'changeStatus']);
        Route::patch('/offer/{customer_offer}', [CustomerController::class, 'updateOffer']);
        Route::delete('/{customer}', [CustomerController::class, 'destroy']);
    }
);
