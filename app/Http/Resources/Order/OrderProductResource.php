<?php

namespace App\Http\Resources\Order;

use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'amount_cny' => $this->amount_cny,
            'amount' => $this->amount,
            'note_number' => $this->note_number,
            'link' => $this->link,
            'classification' => $this->classification,
            'exchange_rate' => getExchangeRate($this->order_id),
            'category' => [
                'id' => $this->category_id,
                'name' => optional(Category::query()->find($this->category_id))->name,
            ],
        ];
    }
}
