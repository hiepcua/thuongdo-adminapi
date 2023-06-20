<?php

namespace App\Http\Resources\Complain;

use App\Constants\ComplainConstant;
use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\ListResource;
use App\Http\Resources\ReportStatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplainDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $orderDetails = $this->orderDetails;
        return [
            'id' => $this->id,
            'order' => [
                'id' => optional($this->order)->id,
                'code' => optional($this->order)->code,
            ],
            'image' => optional($orderDetails->first())->image,
            'type' => optional($this->complainType)->name,
            'time' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_DAY_HI),
            'solution' =>[
                'id' => $this->solution_id,
                'name' => optional($this->solution)->name
            ],
            'status' => new ReportStatusResource(
                $this->status,
                ComplainConstant::STATUSES,
                ComplainConstant::STATUSES_COLOR
            ),
            'images' => optional($this->images)->where('is_bill', 0)->pluck('image'),
            'images_bill' => optional($this->images)->where('is_bill', 1)->pluck('image'),
            'products' => new ListResource($orderDetails, ComplainProductDetailResource::class),
        ];
    }
}
