<?php

namespace App\Http\Resources\Package;

use App\Constants\PackageConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\ListResource;
use App\Http\Resources\Note\NoteResource;
use App\Http\Resources\OnlyIdNameResource;
use App\Http\Resources\ReportStatusResource;
use App\Services\AccountingService;
use App\Services\OrderPackageService;
use App\Services\ReportCustomerService;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrderPackageNote;

class PackageResource extends JsonResource
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
        (new AccountingService())->getInternationShippingCost(
            getProvinceX(optional($this->warehouse)->province_id),
            $this->weight ?? 0,
            ($this->width ?? 0 * $this->height ?? 0 * $this->length ?? 0),
            $isWeight
        );
        $notes= OrderPackageNote::query()->where('order_package_id', $this->id)->where('type', PackageConstant::TYPE_NOTE_NOTE)->orderByDesc('id')->get();
        $noteOrders= OrderPackageNote::query()->where('order_package_id', $this->id)->where('type', PackageConstant::TYPE_NOTE_ORDER)->orderByDesc('id')->get();
        return [
            'info' => [
                'id' => $this->id,
                'order_id' => $this->order_id,
                'bill_code' => $this->bill_code,
                'order_code' => $this->order_code,
                'is_order' => $this->is_order,
                'transporter' => optional($this->transporterRelation)->name ?? $this->transporter,
                'warehouse_code' => optional($this->warehouse)->code,
                'china_shipping_cost' => $this->china_shipping_cost,
                'is_inspection' => (boolean)$this->is_inspection,
                'is_insurance' => (boolean)$this->is_insurance,
                'is_woodworking' => (boolean)$this->is_woodworking,
                'is_delivery' => $this->is_delivery,
                'delivery_id' => $this->delivery_id,
                'wv' => [
                    'weight' => $this->weight ?? 0,
                    'volume' => [
                        'width' => $this->width ?? 0,
                        'height' => $this->height ?? 0,
                        'length' => $this->length ?? 0,
                    ],
                    'type' => $isWeight ? 'weight' : 'volume',
                    'result' => $isWeight ? $this->weight : $this->volume
                ]
            ],
            'customer' => [
                'id' => optional($customer)->id,
                'name' => optional($customer)->name,
                'phone_number' => optional($customer)->phone_number,
                'warehouse' => optional(optional(optional($customer)->warehouse)->province)->name,
                'level' => (new ReportCustomerService())->isNewCustomerByCustomerId(
                    optional($customer)->id
                ) ? 'KH Mới' : null,
                'code' => optional($customer)->code
            ],
            'staffs' => [
                'order' => new OnlyIdNameResource($this->staffOrder),
                'counselor' => new OnlyIdNameResource($this->staffCounselor),
                'care' => new OnlyIdNameResource($this->staffCare),
                'quotation' => new OnlyIdNameResource($this->staffQuotation),
            ],
            'order' => [
                'code_po' => $this->code_po,
                'created_at' => TimeHelper::format($this->created_at),
                'package_number' => $this->package_number,
            ],
            'type' => [
                $this->order_kind_of,
                optional($this->categoryRelation)->name ?? $this->category,
                $this->is_order? optional($this->order)->ecommerce : null,
            ],
            'status' => new ReportStatusResource(
                $this->status,
                PackageConstant::STATUSES,
                PackageConstant::STATUSES_COLOR
            ),
            'costs' => (new OrderPackageService())->getCost($this),
            'notes' => [
                'note' => [
                    'content' => $this->note,
                    'data' => new ListResource($notes, NoteResource::class),
                    'quantity' => $notes->count(),
                ],
                'order' => [
                    'content' => $this->note_ordered,
                    'data' => new ListResource($noteOrders, NoteResource::class),
                    'quantity' => $noteOrders->count(),
                ]
            ],
            "reason_cant_make_delivery" => $msg = $this->getReasonCanNotMakeDelivery($this),
            'can_make_delivery' => is_null($msg),
        ];
    }

    private function getReasonCanNotMakeDelivery($that): ?string
    {
        $isNullDelivery = is_null($that->delivery_id);
        if (!$isNullDelivery) {
            return trans('package.can_not_make_delivery.has_delivery');
        }
        if ($that->status === PackageConstant::STATUS_CANCEL) {
            return trans('package.can_not_make_delivery.cancel');
        }
        if ($statusVN = ($that->status != PackageConstant::STATUS_WAREHOUSE_VN)) {
            return trans('package.can_not_make_delivery.have_not_been_to_VN');
        }
        if (!$that->is_delivery && $statusVN) {
            return trans('package.can_not_make_delivery.is_delivery');
        }
        return null;
    }
}
