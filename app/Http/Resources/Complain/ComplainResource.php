<?php

namespace App\Http\Resources\Complain;

use App\Constants\ComplainConstant;
use App\Helpers\StatusHelper;
use App\Helpers\TimeHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ComplainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $orderDetails = $this->orderDetails;
        return [
            'id' => $this->id,
            'order_code' => optional($this->order)->code,
            'image' => optional($orderDetails->first())->image,
            'packages' => $this->getPackages($orderDetails),
            'complain_type' => optional($this->complainType)->name,
            'solution' => optional($this->solution)->name,
            'status' => StatusHelper::getInfo($this->status, ComplainConstant::class),
            'created_at' => TimeHelper::format($this->created_at),
            'comment_number' => $this->comment_number
        ];
    }

    private function getPackages($orderDetails)
    {
        return optional(optional($orderDetails->first())->orderPackage)->only(['id', 'bill_code']);
    }
}
