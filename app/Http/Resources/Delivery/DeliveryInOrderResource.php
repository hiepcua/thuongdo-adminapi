<?php

namespace App\Http\Resources\Delivery;

use App\Constants\DeliveryConstant;
use App\Constants\TimeConstant;
use App\Helpers\StatusHelper;
use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryInOrderResource extends JsonResource
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
            'id' => $this->id,
            'created_at' => TimeHelper::format($this->created_at, TimeConstant::DATE_VI),
            'date' => TimeHelper::format($this->date, TimeConstant::DATE_VI),
            'transporter' => optional($this->transporter)->name,
            'bill_code' => optional($this->packages)->pluck('bill_code')->all(),
            'delivery_cost' => $this->delivery_cost,
            'shipping_cost' => $this->shipping_cost,
            'shipping_and_order_cost' => $this->shipping_cost + $this->debt_cost,
            'debt_cost' => $this->debt_cost,
            'amount' => $this->delivery_cost == 0 ? 'Chờ báo giá' : $this->amount,
            'payment' => DeliveryConstant::PAYMENTS[$this->payment],
            'is_paid' => $this->payment === DeliveryConstant::PAYMENT_E_WALLET,
            'note' => $this->note,
            'delivery' => [
                'receiver' => $this->receiver,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
            ],
            'status' => StatusHelper::getInfo($this->status, DeliveryConstant::class),
            'is_delete' => $this->status === DeliveryConstant::KEY_STATUS_PENDING
        ];
    }
}
