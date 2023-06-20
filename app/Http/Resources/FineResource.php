<?php

namespace App\Http\Resources;

use App\Constants\FineConstant;
use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class FineResource extends JsonResource
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
            'created_at' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_DAY_HI),
            'info' => [
                'order_code' => $this->order_code,
                'bill_code' => $this->bill_code,
                'is_order' => $this->source_type === Order::class,
                'order_id' => $this->source_id,
            ],
            'amount' => $this->amount,
            'status' => new ReportStatusResource($this->status, FineConstant::STATUSES, FineConstant::STATUSES_COLOR),
            'user' => optional($this->staff)->only('id', 'name', 'code'),
            'cause' => optional($this->cause)->only('id', 'name', 'code'),
            'comment_number' => $this->comment_number,
            'type' => [
                'name' => FineConstant::TYPES[$this->type],
                'value' => $this->type,
            ],
            'reason' => $this->reason,
            'solution' => $this->solution,
            'is_hidden_edit_delete_button' => $this->status === FineConstant::KEY_STATUS_CANCEL
        ];
    }
}
