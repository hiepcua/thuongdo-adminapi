<?php

namespace App\Http\Controllers;

use App\Constants\FineConstant;
use App\Helpers\ValidationHelper;
use App\Http\Requests\Fine\FineChangeStatusRequest;
use App\Http\Requests\FineCommentStoreRequest;
use App\Http\Resources\FineCommentResource;
use App\Http\Resources\ListResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Consignment;
use App\Models\Fine;
use App\Models\FineComment;
use App\Models\Order;
use App\Services\FineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FineController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(FineService $service)
    {
        $this->_service = $service;
    }

    //
    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            "type" => "required|in:".implode(',', array_keys(FineConstant::TYPES)),
            "user_id" => "required|exists:users,id",
            "reason" => "max:500",
            "solution" => "max:500",
            "order_id" => 'nullable|exists:orders,id',
            "order_code" => ['nullable',
                function ($attribute, $value, $fail) {
                    if (!Order::query()->where('code', '=', $value)->exists() && !Consignment::query()->where(
                            'code',
                            '=',
                            $value
                        )->exists()) {
                        $fail(trans('fine.code_not_exists', ['code' => $value]));
                    }
                },
            ],
            "bill_code" => "nullable|exists:order_package,bill_code",
            "amount" => "required|numeric|min:1"
        ];
    }

    /**
     * @return string[]
     */
    protected function getAttributes(): array
    {
        return [
            'type' => 'Loại nộp phạt',
            'user_id' => 'Nhân viên',
            'reason' => 'Nguyên nhân',
            'solution' => 'Giải pháp',
            'order_code' => 'Mã đơn hàng',
            'bill_code' => 'Mã vận đơn',
            'amount' => 'Số tiền',
            'order_id' => 'Mã đơn hàng'
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        $data = $this->storeRequest();
        ValidationHelper::prepareUpdateAction($data, $id);
        return $data;
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getComments(string $id): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(FineComment::query()->where('fine_id', $id)->get(), FineCommentResource::class)
        );
    }

    /**
     * @param  FineCommentStoreRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function addComment(FineCommentStoreRequest $request, string $id): JsonResponse
    {
        $params = $request->all();
        $params['user_id'] = Auth::user()->id;
        $params['fine_id'] = $id;
        Fine::query()->find($id)->increment('comment_number');
        return resSuccessWithinData(new FineCommentResource(FineComment::query()->create($params)));
    }

    /**
     * @param  Fine  $fine
     * @return JsonResponse
     */
    public function cancel(Fine $fine): JsonResponse
    {
        $fine->status = FineConstant::KEY_STATUS_CANCEL;
        $fine->save();
        return resSuccess();
    }

    /**
     * @param  FineChangeStatusRequest  $request
     * @param  Fine  $fine
     * @return JsonResponse
     */
    public function changeStatus(FineChangeStatusRequest $request, Fine $fine): JsonResponse
    {
        $fine->status = $request->status;
        $fine->save();
        return resSuccess();
    }
}
