<?php

namespace App\Http\Resources;

use App\Constants\ComplainConstant;
use App\Constants\NoteConstant;
use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Services\ComplainService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $comments = (new ComplainService())->getCommentsById($this->id);
        $orderDetails = $this->orderDetails;
        return [
            'id' => $this->id,
            'order' => [
                'id' => optional($this->order)->id,
                'code' => optional($this->order)->code,
            ],
            'customer' => [
                'name' => optional($this->customer)->name,
                'phone_number' => optional($this->customer)->phone_number,
            ],
            'image' => optional($orderDetails->first())->image,
            'type' => optional($this->complainType)->name,
            'time' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_DAY_HI),
            'solution' => optional($this->solution)->name,
            'status' => new ReportStatusResource(
                $this->status,
                ComplainConstant::STATUSES,
                ComplainConstant::STATUSES_COLOR
            ),
            'staffs' => [
                'order' => [
                    'info' => new OnlyIdNameResource($this->staffOrder),
                    'feedback' => $this->getFeedback($comments, $this->staff_order_id)
                ],
                'care' => [
                    'info' => new OnlyIdNameResource($this->staffCare),
                    'feedback' => $this->getFeedback($comments, $this->staff_care_id)
                ],
                'complain' => new OnlyIdNameResource($this->staffComplain),
            ],

        ];
    }

    /**
     * @param  Collection  $comments
     * @param  string  $staffId
     * @return array
     */
    private function getFeedback(Collection $comments, ?string $staffId): array
    {
        return [
            'quantity' => $comments->where('type', NoteConstant::TYPE_PUBLIC)->where(
                'cause_id',
                $staffId
            )->count(),
            'seen' => $comments->where('type', NoteConstant::TYPE_PUBLIC)->where(
                'cause_id',
                '!=',
                $staffId
            )->where(
                'is_seen',
                false
            )->count()
        ];
    }
}
