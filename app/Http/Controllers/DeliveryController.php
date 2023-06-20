<?php

namespace App\Http\Controllers;

use App\Constants\DeliveryConstant;
use App\Exports\DeliveryPrintWarehouseExport;
use App\Helpers\ConvertHelper;
use App\Http\Requests\Delivery\ModifiesRequest;
use App\Http\Requests\Delivery\NoteWarehouseRequest;
use App\Http\Resources\Delivery\DeliveryResource;
use App\Http\Resources\Delivery\ListDetailResource;
use App\Http\Resources\Delivery\PrintDeliveryResource;
use App\Http\Resources\Delivery\PrintExWarehouseResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Delivery;
use App\Rules\PackagesWithSameCustomer;
use App\Services\DeliveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller implements StoreValidationInterface
{

    public function __construct(DeliveryService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'transporter_id' => 'required|exists:transporters,id',
            'payment' => 'required|in:'.implode(',', array_keys(DeliveryConstant::PAYMENTS)),
            'date' => 'nullable|date:Y-m-d|after_or_equal:'.date('Y-m-d'),
            'customer_delivery_id' => 'required|uuid',
            'note' => 'string|max:500',
            'packages' => ['required', 'array', new PackagesWithSameCustomer()],
            'packages.*' => 'uuid|exists:order_package,id',
        ];
    }

    protected function getAttributes(): array
    {
        return [
            'customer_id' => 'Khách hàng',
            'transporter_id' => 'Hình thức vận chuyển',
            'payment' => 'Phương thức thanh toán',
            'date' => 'Ngày giao',
            'customer_delivery_id' => 'Địa chỉ giao hàng',
            'note' => 'Ghi chú',
            'packages' => 'Kiện hàng',
            'packages.*' => 'Kiện hàng',
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    /**
     * @param  ModifiesRequest  $request
     * @param  Delivery  $delivery
     * @return JsonResponse
     */
    public function modifies(ModifiesRequest $request, Delivery $delivery): JsonResponse
    {
        return DB::transaction(
            function () use ($delivery, $request) {
                $params = $request->all();
                $params['is_delivery_cost_paid'] = is_numeric(
                    $params['is_delivery_cost_paid']
                ) ? $params['is_delivery_cost_paid'] : null;
                $delivery->update($params);

                if ($request->has('note') && ($note = $request->input('note'))) {
                    $this->_service->storeNote($delivery->id, $note);
                }

                $this->refund($delivery);

                return resSuccessWithinData(new DeliveryResource($delivery));
            }
        );
    }

    /**
     * @param  Delivery  $delivery
     */
    private function refund(Delivery $delivery)
    {
        $refund = json_decode($delivery->refund, true);
        if ($delivery->status === DeliveryConstant::KEY_STATUS_DONE) {
            if ($refund && $refund['amount'] > 0) {
                $this->_service->refundDelivery(
                    $delivery,
                    $refund['amount'],
                    __(
                        'transaction.delivery_refund',
                        [
                            'name' => getCurrentUser()->name,
                            'bill' => implode(',', array_filter($refund['bill_code'])),
                            'amount' => ConvertHelper::numericToVND($refund['amount'])
                        ]
                    )
                );
            }

            if ($delivery->extend_cost > 0 && $delivery->is_paid_extend) {
                $this->_service->refundDelivery(
                    $delivery,
                    $delivery->extend_cost,
                    __(
                        'transaction.delivery_extend_cost',
                        [
                            'name' => getCurrentUser()->name,
                            'amount' => ConvertHelper::numericToVND($delivery->extend_cost)
                        ]
                    )
                );
            }
        }
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function detail(string $id): JsonResponse
    {
        return resSuccessWithinData(new ListDetailResource(Delivery::query()->findOrFail($id)));
    }

    public function printDelivery(Delivery $delivery): JsonResponse
    {
        return resSuccessWithinData(new PrintDeliveryResource($delivery));
    }

    public function printExWarehouse(Delivery $delivery): JsonResponse
    {
        return resSuccessWithinData(new PrintExWarehouseResource($delivery));
    }

    public function getXlsx(Delivery $delivery)
    {
        return (new DeliveryPrintWarehouseExport($delivery))->download('PhieuInKho_' . $delivery->code . '_' . time() . '.xlsx');
    }

    public function storeNote(NoteWarehouseRequest $request, string $id): JsonResponse
    {
        (new DeliveryService())->storeNote($id, $request->input('note'));
        return resSuccess();
    }
}
