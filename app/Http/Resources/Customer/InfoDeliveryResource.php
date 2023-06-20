<?php

namespace App\Http\Resources\Customer;

use App\Helpers\ConvertHelper;
use App\Models\ReportCustomer;
use Illuminate\Http\Resources\Json\JsonResource;

class InfoDeliveryResource extends JsonResource
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
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'warehouse_code' => optional($this->warehouse)->code,
            'balance_amount' => optional(ReportCustomer::query()->where('customer_id', $this->id)->first())->balance_amount ?? 0
        ];
    }
}
