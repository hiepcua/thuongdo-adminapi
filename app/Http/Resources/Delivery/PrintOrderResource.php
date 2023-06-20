<?php

namespace App\Http\Resources\Delivery;

use App\Helpers\AccountingHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintOrderResource extends JsonResource
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
            'code' => $this['code'],
            'amount' => AccountingHelper::getCosts($this['total_amount']),
            'deposit_cost' => $this['deposit_cost'],
            'balance' => $this['balance']
        ];
    }
}
