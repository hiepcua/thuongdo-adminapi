<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteOrderDetailStoreRequest;
use App\Http\Requests\NoteOrderRequest;
use App\Http\Resources\Note\OrderNoteResource;
use App\Models\OrderDetail;
use App\Models\OrderDetailNote;
use App\Models\OrderNote;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    public function __construct(NoteService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getOrderDetail(string $id): JsonResponse
    {
        return $this->getList(OrderDetailNote::class, $id, 'order_detail_id');
    }

    /**
     * @param  NoteOrderDetailStoreRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function storeOrderDetail(NoteOrderDetailStoreRequest $request, string $id): JsonResponse
    {
        return $this->storeNote(OrderDetailNote::class, $id, 'order_detail_id');
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getOrderPublic(string $id): JsonResponse
    {
        return $this->getList(OrderNote::class, $id, 'order_id', true);
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getOrderPrivate(string $id): JsonResponse
    {
        return $this->getList(OrderNote::class, $id, 'order_id', false);
    }

    /**
     * @param  NoteOrderDetailStoreRequest  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function storeOrder(NoteOrderRequest $request, string $id): JsonResponse
    {
        return $this->storeNote(OrderNote::class, $id, 'order_id');
    }

    /**
     * @param  string  $model
     * @param  string  $id
     * @param  string  $column
     * @param  bool|null  $isPublic
     * @return JsonResponse
     */
    private function getList(string $model, string $id, string $column, ?bool $isPublic = null): JsonResponse
    {
        $query = (new $model)::query()->where($column, $id);
        if (!is_null($isPublic)) {
            $query->where('is_public', $isPublic);
        }
        return resSuccessWithinData(
            $query->get()->map(
                function ($item) use ($column) {
                    return new OrderNoteResource($item, $column);
                }
            )
        );
    }

    /**
     * @param  string  $model
     * @param  string  $id
     * @param  string  $column
     * @param  bool|null  $isPublic
     * @return JsonResponse
     */
    private function storeNote(string $model, string $id, string $column): JsonResponse
    {
        $data = ['id' => $id, 'column' => $column];
        if($model === OrderDetailNote::class) {
            $data['order_id'] = optional(OrderDetail::query()->find($data['id']))->order_id;
        }
        return resSuccessWithinData(
            new OrderNoteResource(
                $this->_service->store(
                    $model,
                    $data + request()->only('content', 'supplier_id', 'is_public')
                ),
                $column
            )
        );
    }
}
