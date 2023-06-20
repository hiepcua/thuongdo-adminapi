<?php

namespace App\Http\Resources\CustomerDelivery;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class CustomerDeliveryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'receiver' => $this->receiver,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'ward_id' => $this->ward_id,
            'district_id' => $this->district_id,
            'province_id' => $this->province_id,
            'is_default' => $this->is_default,
            'custom_name' => $this->custom_name
        ];
    }
}
