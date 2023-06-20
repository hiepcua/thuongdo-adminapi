<?php

namespace App\Http\Resources;

use App\Constants\CustomerConstant;
use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
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
            'time' => TimeHelper::format($this->created_at),
            'info' => $this->info,
            'account_number' => $this->account_number,
            'account_holder' => $this->account_holder,
            'bank' => $this->bank,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'branch' => $this->branch,
            'customer' => optional($this->customer)->only('id', 'name', 'code', 'phone_number'),
            'status' => new ReportStatusResource(
                $this->status,
                CustomerConstant::WITHDRAWAL_STATUSES,
                CustomerConstant::WITHDRAWAL_COLOR
            ),
        ];
    }
}
