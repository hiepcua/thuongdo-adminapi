<?php

namespace App\Http\Resources\Package;

use App\Helpers\AccountingHelper;
use App\Models\OrderPackage;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductItemResource extends JsonResource
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
            'image' => $this->image,
            'name' => $this->name,
            'quantity' => $quantity = $this->pivot->quantity,
            'unit_price_cny' => $this->unit_price_cny,
            'unit_price' => $this->unit_price,
            'amount_cny' => AccountingHelper::getCosts($this->unit_price_cny * $quantity),
            'amount' => AccountingHelper::getCosts($this->unit_price * $quantity),
            'classification' => $this->classification,
            'exchange_rate' => (float)(optional(OrderPackage::query()->find($this->pivot->order_package_id))->exchange_rate ?? getExchangeRate()),
        ];
    }
}
