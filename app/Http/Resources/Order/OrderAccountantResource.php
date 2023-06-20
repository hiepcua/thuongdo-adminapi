<?php

namespace App\Http\Resources\Order;

use App\Constants\ColorConstant;
use App\Constants\OrderConstant;
use App\Constants\TimeConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\TimeHelper;
use App\Http\Resources\Complain\ComplainResource;
use App\Http\Resources\CustomerDelivery\CustomerDeliveryResource;
use App\Http\Resources\Delivery\DeliveryInOrderResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\OnlyIdNameResource;
use App\Http\Resources\Package\OrderPackageResource;
use App\Http\Resources\ReportStatusResource;
use App\Models\CustomerDelivery;
use App\Models\OrderStatusTime;
use App\Models\Staff;
use App\Services\ComplainService;
use App\Services\DeliveryService;
use App\Services\OrderPackageService;
use App\Services\ReportCustomerService;
use Illuminate\Http\Resources\Json\JsonResource;

// Phục vụ cho màn bên kế toán nó ít dữ liệu hơn
class OrderAccountantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $customer = $this->customer;
        return [
            'info' => [
                'id' => $this->id,
                'code' => $this->code, // Mã đơn hàng
                'code_po' => $this->code_po, // Mã đặt hàng
                'created_at' => TimeHelper::format($this->created_at, TimeConstant::DATETIME_BY_DAY_HI),
                'exchange_rate' => $this->exchange_rate,
                'status' => new ReportStatusResource(
                    $this->status,
                    OrderConstant::STATUSES,
                    OrderConstant::STATUSES_COLOR
                ),
            ],
            'staff' => [
                'order' => new OnlyIdNameResource($this->staffOrder),
            ],
            'accounting' => $this->only(
                'order_cost',
                'china_shipping_cost',

                'order_cost_old',
                'china_shipping_cost_old',
            ),
            // Custom dữ liệu
            'exchange_rate' => $this->exchange_rate,
            'order_cost' => $this->order_cost / $this->exchange_rate,
            'order_cost_old' => $this->order_cost_old / $this->exchange_rate,

            'china_shipping_cost' => $this->china_shipping_cost,
            'china_shipping_cost_old' => (int)$this->china_shipping_cost_old,

            'diff_china_shipping_cost' => $this->china_shipping_cost - (int)$this->china_shipping_cost_old,
            'diff_order_cost' => ($this->order_cost - $this->order_cost_old) / $this->exchange_rate,

            'diff_order_and_shipping_cost' => $this->getDiff(),
            'diff_order_and_shipping_cost_abs' => abs($this->getDiff()),
        ];
    }

    public function getDiff()
    {
        $diff_china_shipping_cost = $this->china_shipping_cost - (int)$this->china_shipping_cost_old;
        $diff_order_cost = ($this->order_cost - $this->order_cost_old) / $this->exchange_rate;

        return $diff_china_shipping_cost + $diff_order_cost;
    }

    /**
     * @param  string  $orderId
     * @param  int  $quantity
     * @param  string  $object
     * @return string
     */
    public function getColorByObject(string $orderId, int $quantity, string $object): string
    {
        switch ($object) {
            case 'packages':
                return $this->getColor((new OrderPackageService())->getStatusesDone($orderId) === $quantity);
            case 'complains':
                return $this->getColor((new ComplainService())->getStatusesDone($orderId) === $quantity);
            // Deliveries
            default:
                return $this->getColor((new DeliveryService())->getStatusesDone($orderId) === $quantity);
        }
    }

    /**
     * @param  bool  $isDone
     * @return string
     */
    private function getColor(bool $isDone): string
    {
        $red = ColorConstant::RED;
        $green = ColorConstant::GREEN;
        if ($isDone) {
            return $green;
        }
        return $red;
    }
}
