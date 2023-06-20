<?php

namespace App\Http\Resources\Customer;

use App\Constants\CustomerConstant;
use App\Constants\TimeConstant;
use App\Constants\ViaConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\CustomerDelivery\CustomerDeliveryResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\OnlyIdNameResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $report = $this->report;
        return [
            'id' => $this->id,
            'code' => $this->code,
            'info' => [
                'name' => $this->name,
                'email' => $this->email,
                'address' => optional(optional($this->warehouse)->province)->name,
                'gender' => CustomerConstant::GENDER[$this->gender ?? CustomerConstant::GENDER_UNDEFINED],
                'phone_number' => $this->phone_number,
                'created_at' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_HI_DAY),
                'via' => $this->via ? ViaConstant::STATUSES[$this->via] : null,
                'level' => $this->level,
                'bod' => $this->bod,
                'warehouse_id' => $this->warehouse_id,
                'service' => $this->service,
                'staff_care_id' => $this->staff_care_id,
                'staff_counselor_id' => $this->staff_counselor_id,
                'business_type' => [
                    'value' => $this->business_type,
                    'name' => CustomerConstant::CUSTOMER_BUSINESS_TYPE[$this->business_type] ?? null
                ],
            ],

            'order' => [
                'orders_number' => (int)($report->orders_number ?? 0),
                'order_consignment_number' => (int)($report->consignment_number ?? 0),
                'order_packages_number' => (int)($report->packages_number ?? 0)
            ],
            'transaction' => [
                'deposited_amount' => (float)($report->deposited_amount ?? 0),
                'balance_amount' => (float)($report->balance_amount ?? 0),
            ],
            'status' => $this->status,
            'staff' => [
                'order' =>new OnlyIdNameResource($this->staffOrder),
                'sale' => new OnlyIdNameResource($this->staffSale),
                'care' => new OnlyIdNameResource($this->staffCare),
                'counselor' => new OnlyIdNameResource($this->staffCounselor),
            ],
            'label' => new OnlyIdNameResource($this->label),
            'offer' => $this->offer,
            'delivery' => (new ListResource($this->delivery, CustomerDeliveryResource::class))
        ];
    }
}
