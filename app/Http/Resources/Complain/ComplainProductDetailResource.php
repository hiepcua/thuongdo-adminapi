<?php

namespace App\Http\Resources\Complain;

use App\Models\Complain;
use App\Models\ComplainDetail;
use App\Models\ComplainImage;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplainProductDetailResource extends JsonResource
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
            'bill_code' => optional($this->orderPackage)->bill_code,
            'image' => $this->image,
            'name' => $this->name,
            'unit_price_cny' => $this->unit_price_cny,
            'quantity' => $this->quantity,
            'amount_cny' => $this->amount_cny,
            'complain_note' => $this->pivot->note,
            'exchange_rate' => getExchangeRate($this->order_id),
            'images' => optional($this->images)->pluck('image')
        ];
    }
}
