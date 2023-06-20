<?php

namespace App\Http\Resources\Order;

use App\Constants\OrderConstant;
use App\Http\Resources\ListResource;
use App\Models\OrderSupplier;
use App\Models\Supplier;
use App\Services\NoteService;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource->map(
            function ($item, $key) {
                $orderId  = $item->first()->order_id;
                $orderSupplier = OrderSupplier::query()->where(
                    ['order_id' => $orderId, 'supplier_id' => $key]
                )->get();
                $accounting = [
                    'woodworking_cost' => $orderSupplier->sum('woodworking_cost'),
                    'china_shipping_cost' => $orderSupplier->sum('china_shipping_cost'),
                    'order_fee' => $orderSupplier->sum('order_fee'),
                    'order_cost' => $orderSupplier->sum('order_cost'),
                    'inspection_cost' => $orderSupplier->sum('inspection_cost'),
                    'discount_cost' => $orderSupplier->sum('discount_cost'),
                    'international_shipping_cost' => $orderSupplier->sum('international_shipping_cost')
                ];
                return [
                    'suppliers' => $this->getSuppliers($orderSupplier, $key),
                    'products' => new ListResource($item, OrderProductResource::class),
                    'accounting' => $accounting + ['total_amount' => array_sum(array_values($accounting))],
                    'note' => [
                        'public' => (new NoteService())->getOrderNote($orderId, $key, true),
                        'private' => (new NoteService())->getOrderNote($orderId, $key, false),
                    ],
                ];
            }
        )->values();
    }

    /**
     * @param $orderSupplier
     * @param  string  $key
     * @return array
     */
    private function getSuppliers($orderSupplier, string $key): array
    {
        $suppliers = Supplier::query()->find($key)->only('id', 'name');
        $orderSupplier = $orderSupplier->first();
        $services = [
            'delivery_type' => [
                'value' => optional($orderSupplier)->delivery_type,
                'label' => OrderConstant::DELIVERIES_TEXT[optional($orderSupplier)->delivery_type] ?? null
            ],
            'services' => [
                'is_woodworking' => (bool)(optional($orderSupplier)->is_woodworking ?? null),
                'is_inspection' => (bool)(optional($orderSupplier)->is_inspection ?? null),
                'is_shock_proof' => (bool)(optional($orderSupplier)->is_shock_proof ?? null)
            ]
        ];
        return array_merge(
            $suppliers,
            $services
        );
    }
}
