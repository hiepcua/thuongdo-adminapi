<?php

use App\Http\Controllers\FundController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/',[FundController::class, 'pagination'])->name('fund.pagination');
    Route::get('/list',[FundController::class, 'list'])->name('fund.list');
    Route::post('/init-default',[FundController::class, 'initDefault'])->name('fund.init-default');
});
