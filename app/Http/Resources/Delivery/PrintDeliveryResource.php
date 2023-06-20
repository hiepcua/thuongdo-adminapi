<?php

namespace App\Http\Resources\Delivery;

use App\Constants\TimeConstant;
use App\Constants\DeliveryConstant;
use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintDeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'date' => TimeHelper::format($this->date, TimeConstant::DATE_VI),
            'sender' => $this->organization->only('name', 'phone_number') + ['address' => optional(optional(optional($this->orderPackages->first())->warehouse))->custom_name_second],
            'receiver' => [
                'name' => $this->customerDelivery->receiver,
                'address' => $this->customerDelivery->custom_name_second,
                'phone_number' => $this->customerDelivery->phone_number
            ],
            'note' => $this->note_customer,
            'cost' => [
                'delivery' => $this->payment == DeliveryConstant::PAYMENT_E_WALLET ? 0 : $this->delivery_cost,
                'collection' => $this->payment == DeliveryConstant::PAYMENT_E_WALLET ? 0 : $this->amount - $this->delivery_cost
            ],
            'transporter' => optional($this->transporter)->name
        ];
    }
}
