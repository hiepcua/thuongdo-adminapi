<?php

namespace App\Http\Resources\Package;

use App\Constants\ColorConstant;
use App\Constants\PackageConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\StatusHelper;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Services\AccountingService;
use App\Services\OrderPackageService;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $exchangeRate = optional($this->order)->exchange_rate ?? 1;
        $weight = $this->weight ?? 0;
        $volume = $this->volume ?? 0;
        (new AccountingService())->getWoodworkingCost(0, $volume, $isWeightGreaterThan);
        $object = $this->order;
        $detail = $this->consignmentDetail ?? $this->orderDetail;
        return [
            'id' => $this->id,
            'order_id' => optional($object)->id,
            'order_code' => optional($object)->code,
            'bill_code' => $this->bill_code,
            'warehouse_code' => optional($this->warehouse)->code,
            'is_inspection' => (boolean)$this->is_inspection,
            'is_insurance' => (boolean)$this->is_insurance,
            'is_woodworking' => (boolean)$this->is_woodworking,
            'is_delivery' => $this->is_delivery,
            'delivery_cost' => $this->delivery_cost,
            'delivery_cost_cny' => AccountingHelper::getCosts($this->delivery_cost / $exchangeRate),
            'international_shipping_cost' => $this->international_shipping_cost,
            'international_shipping_cost_cny' => AccountingHelper::getCosts($this->international_shipping_cost / $exchangeRate),
            'china_shipping_cost' => $this->china_shipping_cost ?? 0,
            'china_shipping_cost_cny' => AccountingHelper::getCosts(($this->china_shipping_cost ?? 0) / $exchangeRate),
            'shipping_cost' => $this->shipping_cost ?? 0,
            'inspection_cost' => $this->inspection_cost ?? 0,
            'insurance_cost' => $this->insurance_cost ?? 0,
            'woodworking_cost' => $this->woodworking_cost,
            'shock_proof_cost' => $this->shock_proof_cost,
            'storage_cost' => $this->storage_cost,
            'weight' => $weight,
            'volume' => $volume,
            'weight_or_volume' => $isWeightGreaterThan ? "$weight kg" : "$volume m³",
            'discount_cost' => (float)$this->discount_cost ?? 0,
            'discount_percent' => (float)$this->discount_cost ?? 0,
            'is_order' => (bool)$this->is_order,
            'type_of_goods' => [
                'name' => $this->is_order ? 'Order' : 'Hàng ký gửi',
                'color' => $this->is_order ? ColorConstant::CARROT_ORANGE : ColorConstant::GREEN
            ],
            'amount' => $amount = $this->amount ?? 0,
            'amount_cny' => AccountingHelper::getCosts($amount / $exchangeRate),
            'note' => $this->note,
            'description' => $this->description,
            'transporter' => $this->transporter,
            'address_receiver' => optional($this->customerDelivery)->custom_name,
            'quantity' => optional($detail)->quantity,
            'packages_number' => optional($detail)->packages_number,
            'status' => $status = (new OrderPackageService())->getStatus($this->status) + StatusHelper::getTime(
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
            "reason_cant_make_delivery" => $msg = $this->getReasonCanNotMakeDelivery($this),
            'can_make_delivery' => is_null($msg),
            'has_complain' => OrderDetail::query()->where(['order_package_id' => $this->id])->whereNotNull(
                'complain_id'
            )->exists(),
            'is_delete' => $this->isDelete($status['name'])
        ];
    }
    /**
     * @param  string  $status
     * @return bool
     */
    private function isDelete(string $status): bool
    {
        return (array_search($status, array_values(PackageConstant::STATUSES)) ?? 0) < PackageConstant::INDEX_STATUS_WAITING_CODE;
    }

    /**
     * @param $that
     * @return string
     */
    private function getReasonCanNotMakeDelivery($that): ?string
    {
        $isNullDelivery = is_null($that->delivery_id);
        if (!$isNullDelivery) {
            return trans('package.can_not_make_delivery.has_delivery');
        }
        if ($that->status === PackageConstant::STATUS_CANCEL) {
            return trans('package.can_not_make_delivery.cancel');
        }
        if ($statusVN = ($that->status != PackageConstant::STATUS_WAREHOUSE_VN)) {
            return trans('package.can_not_make_delivery.have_not_been_to_VN');
        }
        if (!$that->is_delivery && $statusVN) {
            return trans('package.can_not_make_delivery.is_delivery');
        }
        return null;
    }
}
