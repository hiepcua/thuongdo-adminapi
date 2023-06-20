<?php

namespace App\Http\Resources\Order;

use App\Constants\ColorConstant;
use App\Constants\OrderConstant;
use App\Constants\TimeConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\TimeHelper;
use App\Http\Resources\Complain\ComplainResource;
use App\Http\Resources\CustomerDelivery\CustomerDeliveryResource;
use App\Http\Resources\Delivery\DeliveryInOrderResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\OnlyIdNameResource;
use App\Http\Resources\Package\OrderPackageResource;
use App\Http\Resources\ReportStatusResource;
use App\Models\CustomerDelivery;
use App\Models\OrderStatusTime;
use App\Models\Staff;
use App\Services\ComplainService;
use App\Services\DeliveryService;
use App\Services\OrderPackageService;
use App\Services\ReportCustomerService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $customer = $this->customer;
        return [
            'info' => [
                'id' => $this->id,
                'code' => $this->code,
                'created_at' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_DAY_HI),
                'status_at' => TimeHelper::format(
                    optional(OrderStatusTime::query()->where('order_id', $this->id)->first())->{$this->status},
                    TimeConstant::DATETIME_BY_DAY_HI
                ),
                'notes' => $this->note_number,
                'order_percent' => $this->order_percent,
                'total_amount' => $this->total_amount,
                'deposit_amount' => $depositAmount = $this->deposit_cost ?? 0,
                'purchase_amount' => AccountingHelper::getCosts($this->total_amount - $depositAmount),
                'packages_number' => $this->packages_number,
                'exchange_rate' => $this->exchange_rate,
                'status' => new ReportStatusResource(
                    $this->status,
                    OrderConstant::STATUSES,
                    OrderConstant::STATUSES_COLOR
                ),
                'warehouse' => optional($this->warehouse)->only('id', 'custom_name', 'province_id'),
                'customer_delivery' => optional($this->customerDelivery)->only('id', 'custom_name', 'province_id', 'receiver'),
            ],
            'customer' => [
                'id' => optional($customer)->id,
                'name' => optional($customer)->name,
                'phone_number' => optional($customer)->phone_number,
                'warehouse' => optional(optional(optional($customer)->warehouse)->province)->name,
                'level' => (new ReportCustomerService())->isNewCustomerByCustomerId(optional($customer)->id) ? 'KH Mới' : null,
                'deliveries' => new ListResource(
                    CustomerDelivery::query()->where('customer_id', optional($customer)->id)->get(),
                    CustomerDeliveryResource::class
                )
            ],
            'staff' => [
                'order' => new OnlyIdNameResource($this->staffOrder),
                'sale' => new OnlyIdNameResource($this->staffSale),
                'care' => new OnlyIdNameResource($this->staffCare),
                'counselor' => new OnlyIdNameResource($this->staffCounselor),
                'quotation' => new OnlyIdNameResource($this->staffQuotation),
            ],
            'note' => [
                'public' => optional(
                    $this->notePublic->where('subject_type', Staff::class)->sortByDesc(
                        'created_at'
                    )->first()
                )->only(['id', 'content']),
                'private' => optional($this->notePrivate->sortByDesc('created_at')->first())->only(['id', 'content']),
            ],
            'accounting' => $this->only(
                'order_fee',
                'woodworking_cost',
                'discount_cost',
                'inspection_cost',
                'china_shipping_cost',
                'international_shipping_cost',
                'order_cost',
                'total_amount',
                'deposit_cost',
            ),
            'reports' => [
                'packages' => [
                    'quantity' => $this->packages_number,
                    'color' => $this->getColorByObject($this->id, $this->packages_number, 'packages'),
                    'data' => new ListResource(
                        $this->packages,
                        OrderPackageResource::class
                    )
                ],
                'complains' => [
                    'quantity' => $this->complains_number,
                    'color' => $this->getColorByObject($this->id, $this->complains_number, 'complains'),
                    'data' => new ListResource(
                        $this->complains,
                        ComplainResource::class
                    )
                ],
                'deliveries' => [
                    'quantity' => $this->deliveries_number,
                    'color' => $this->getColorByObject($this->id, $this->deliveries_number, 'deliveries'),
                    'data' => new ListResource(
                        $this->deliveries,
                        DeliveryInOrderResource::class
                    )
                ]
            ],
            'is_cancel' => in_array(
                $this->status,
                [OrderConstant::KEY_STATUS_WAITING_QUOTE, OrderConstant::KEY_STATUS_WAITING_DEPOSIT]
            ),

            // Thêm key error_logs bên kế toán
            'error_log' => $this->getErrorLog()
        ];
    }

    /**
     * @param  string  $orderId
     * @param  int  $quantity
     * @param  string  $object
     * @return string
     */
    public function getColorByObject(string $orderId, int $quantity, string $object): string
    {
        switch ($object) {
            case 'packages':
                return $this->getColor((new OrderPackageService())->getStatusesDone($orderId) === $quantity);
            case 'complains':
                return $this->getColor((new ComplainService())->getStatusesDone($orderId) === $quantity);
            // Deliveries
            default:
                return $this->getColor((new DeliveryService())->getStatusesDone($orderId) === $quantity);
        }
    }

    /**
     * @param  bool  $isDone
     * @return string
     */
    private function getColor(bool $isDone): string
    {
        $red = ColorConstant::RED;
        $green = ColorConstant::GREEN;
        if ($isDone) {
            return $green;
        }
        return $red;
    }

    private function getErrorLog()
    {
        if ($this->error_logs == null) {
            return null;
        }
        $error_logsDB = array_reverse(json_decode($this->error_logs));
        return $error_logsDB[0];

    }
}
