<?php

namespace App\Http\Resources\Delivery;

use App\Constants\DeliveryConstant;
use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\Customer\InfoDeliveryResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\Note\NoteResource;
use App\Http\Resources\ReportStatusResource;
use App\Models\DeliveryNote;
use App\Models\Staff;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $notes = $this->notesStaff;
        return [
            'id' => $this->id,
            'time' => TimeHelper::format($this->created_at),
            'customer' => new InfoDeliveryResource($this->customer),
            'status' => new ReportStatusResource(
                $this->status,
                DeliveryConstant::STATUSES,
                DeliveryConstant::STATUSES_COLOR
            ),
            'info' => [
                'carrier' => optional($this->transporter)->name,
                'transporter_detail' => optional($this->transporterDetail)->only('id', 'name', 'phone_number'),
                'payment' => DeliveryConstant::PAYMENTS[$this->payment],
                'receiver' => optional($this->customerDelivery)->custom_name,
                'packages' => [
                    'quantity' => optional($this->orderPackages)->count() ?? 0,
                    'data' => $this->orderPackages->map(fn($item) => $item->only('id', 'bill_code'))
                ],
                'date' => TimeHelper::format($this->date, TimeConstant::DATE_VI),
                'delivery' => [
                    'delivery_cost' => $this->delivery_cost,
                    'is_delivery_cost_paid' => is_null($this->is_delivery_cost_paid) ? 'undefined' : (int)$this->is_delivery_cost_paid,
                ],
                'notes' => [
                    'customer' => $this->note_customer,
                    'staff' => $this->note,
                    'staff_quantity' => $notes->count(),
                    'data' => new ListResource($notes, NoteResource::class)
                ],
                'info_po_shi' => [
                    'postcode' => $this->postcode,
                    'shipper_phone_number' => $this->shipper_phone_number,
                ]
            ]
        ];
    }
}
