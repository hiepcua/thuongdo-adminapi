<?php

namespace App\Http\Resources\Package;

use App\Constants\PackageConstant;
use App\Helpers\StatusHelper;
use App\Models\OrderDetailPackage;
use App\Models\OrderPackage;
use App\Services\OrderPackageService;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $receiver = optional($this->order)->customerDelivery;
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'is_order' => $this->is_order,
            'code_po' => $this->code_po,
            'bill_code' => $this->bill_code,
            'category' => [
                'other' => $this->category,
                'id' => $this->category_id,
                'name' => optional($this->categoryRelation)->name
            ],
            'transporter' => [
                'name' => optional($this->transporterRelation)->name,
                'id' => $this->transporter_id,
                'other' => $this->transporter
            ],
            'package_number' => $this->package_number ?? 0,
            'status' => (new OrderPackageService())->getStatus($this->status) + StatusHelper::getTime(
                    $this->id,
                    OrderPackage::class,
                    $this->status
                ),
            'statuses' => StatusHelper::getStatuses(
                $this->id,
                OrderPackage::class,
                PackageConstant::class,
                PackageConstant::STATUES_SHOW_DETAILS
            ),
            'info' => [
                'order_code' => $this->order_code,
                'order_kind_of' => $this->order_kind_of,
                'weight' => $this->weight,
                'volume' => $this->volume,
            ],
            'costs' => (new OrderPackageService())->getCost($this),

            'receiver' => [
                'name' => optional($receiver)->custom_name,
                'inspection_number' => $this->quantity,
                'product_number' => (int)OrderDetailPackage::query()->where('order_package_id', $this->id)->sum('quantity'),
            ],

            'note' => $this->note

        ];
    }
}
