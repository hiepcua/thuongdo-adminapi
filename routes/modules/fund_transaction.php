<?php

use App\Http\Controllers\FundTransactionController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/',[FundTransactionController::class, 'pagination'])->name('fund.pagination');

    // Thêm mới giao dịch
    Route::get('/get-category-vi', [FundTransactionController::class, 'getCategoryVi']);
    Route::get('/get-category-cn', [FundTransactionController::class, 'getCategoryCn']);
    Route::post('/get-customer', [FundTransactionController::class, 'getCustomer']);
    Route::post('/', [FundTransactionController::class, 'store']);

    // Xóa giao dịch
    Route::post('hide/{id}', [FundTransactionController::class, 'hide']);

    Route::put('/{id}', [FundTransactionController::class, 'update']);

    // Thêm mới chuyển quỹ
    Route::post('/save-transfer-fund', [FundTransactionController::class, 'saveTransferFund']);

    // Đổi tiền TQ cho khách hàng
    Route::post('/save-money-change', [FundTransactionController::class, 'saveMoneyChange']);

    // Nạp tiền ngân hàng Trung Quốc
    Route::post('/save-money-recharge', [FundTransactionController::class, 'saveMoneyReCharge']);
    Route::get('/save-money-recharge-get-category', [FundTransactionController::class, 'saveMoneyReChargeGetCategory']);

    // Rút tiền
    Route::post('/save-money-withdrawal', [FundTransactionController::class, 'saveMoneyWithdrawal']);

    // Danh sách Chi tiết đặt hàng thực tế nhà cung cấp
    Route::get('/list-supplier-order-details', [FundTransactionController::class, 'listSupplierOrderDetails']);
    // Xác nhận đặt hàng
    Route::post('/order-confirmation', [FundTransactionController::class, 'orderConfirmation']);
    // Báo lỗi
    Route::post('/order-error', [FundTransactionController::class, 'orderError']);

    // Tạo nạp tiền Fake từ ngân hàng
    Route::post('/fake-save', [FundTransactionController::class, 'fakeSave']);
});
