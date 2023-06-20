<?php

namespace App\Http\Controllers;

use App\Constants\CustomerConstant;
use App\Constants\TransactionConstant;
use App\Helpers\ConvertHelper;
use App\Helpers\PaginateHelper;
use App\Http\Requests\Transaction\WithdrawalChangeStatusRequest;
use App\Http\Resources\PaginateJsonResource;
use App\Http\Resources\WithdrawalResource;
use App\Models\CustomerWithdrawal;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function getWithdrawal(): JsonResponse
    {
        return resSuccessWithinData(
            new PaginateJsonResource(
                CustomerWithdrawal::query()->paginate(PaginateHelper::getPerPage()),
                WithdrawalResource::class
            )
        );
    }

    /**
     * @param  WithdrawalChangeStatusRequest  $request
     * @param  CustomerWithdrawal  $withdrawal
     * @return JsonResponse
     */
    public function withdrawalChangeStatus(
        WithdrawalChangeStatusRequest $request,
        CustomerWithdrawal $withdrawal
    ): JsonResponse {
        $withdrawal->status = $status = $request->input('status');
        $withdrawal->save();
        if ($status === CustomerConstant::KEY_WITHDRAWAL_STATUS_CANCEL) {
            (new TransactionService())->setTransactionIncrement(
                $withdrawal->amount,
                TransactionConstant::STATUS_REFUND,
                trans(
                    'transaction.withdrawal_cancel',
                    [
                        'name' => 'Nhân viên '.getCurrentUser()->name,
                        'code' => $withdrawal->code,
                        'amount' => ConvertHelper::numericToVND($withdrawal->amount)
                    ]
                ),
                $withdrawal->customer_id,
                $withdrawal
            );
        }
        return resSuccess();
    }

    /**
     * @param  CustomerWithdrawal  $withdrawal
     * @return JsonResponse
     */
    public function getWithdrawalDetail(CustomerWithdrawal $withdrawal): JsonResponse
    {
        return resSuccessWithinData(new WithdrawalResource($withdrawal));
    }
}
