<?php

use App\Http\Controllers\MockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'mock'], function() {
    Route::get('/handle', [MockController::class, 'handle']);
    Route::get('/package/{order}', [MockController::class, 'orderPackage']);
    Route::patch('/package/{id}', [MockController::class, 'orderPackageUpdateStatus']);
    Route::patch('/order/{order}', [MockController::class, 'orderUpdateStatus']);
    Route::patch('/complain/{complain}', [MockController::class, 'complainUpdateStatus']);
    Route::patch('/deposit/{customerId}', [MockController::class, 'depositMoney']);
    Route::patch('/consignment/{id}', [MockController::class, 'consignmentStatus']);
    Route::patch('/withdrawal/{id}', [MockController::class, 'withdrawalStatus']);
});
