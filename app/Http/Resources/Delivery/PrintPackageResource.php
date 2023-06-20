<?php

namespace App\Http\Resources\Delivery;

use App\Helpers\AccountingHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $unitStr = $this->volume > 0 ? 'm3' : 'kg';
        return [
            'bill_code' => $this->bill_code,
            'unit' => ($unit = $this->volume > 0 ? $this->volume : $this->weight) . $unitStr,
            'unit_price_cost' => $unit ? AccountingHelper::getCosts($this->international_shipping_cost / $unit) : 0,
            'international_shipping_cost' => $this->international_shipping_cost,
            'china_shipping_cost' => $this->china_shipping_cost,
            'inspection_cost' => $this->inspection_cost,
            'insurance_cost' => $this->insurance_cost,
            'woodworking_cost' => $this->woodworking_cost,
            'discount_cost' => $this->discount_cost,
            'shock_proof_cost' => $this->shock_proof_cost,
            'delivery_cost' => $this->delivery_cost,
            'storage_cost' => $this->storage_cost,
            'amount' => $this->amount
        ];
    }
}
