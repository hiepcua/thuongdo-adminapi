<?php

namespace Database\Factories;

use App\Constants\LocateConstant;
use App\Constants\PackageConstant;
use App\Helpers\RandomHelper;
use App\Models\Order;
use App\Services\AccountingService;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $weight = 0.5;
        $volume = round(mt_rand() / mt_getrandmax(), 2) / 10;
        $isInspection = $this->faker->boolean();
        $isInsurance = $this->faker->boolean();
        $isWoodworking = $this->faker->boolean();
        $accountingService = new AccountingService();
        $order = Order::query()->first();
        $array = [
            0,
            rand(0, 300000),
            $accountingService->getInternationShippingCost(
                getProvinceX(optional($order->warehouse)->province_id),
                $weight,
                $volume
            ),
            $accountingService->getInspectionCost(1),
            rand(1, 30000),
            $accountingService->getWoodworkingCost($weight, $volume)
        ];
        return [
            'order_id' => $order->id,
            'order_type' => get_class($order),
            'customer_id' => $order->customer_id,
            'customer_delivery_id' => $order->customer_delivery_id,
            'bill_code' => RandomHelper::billCode(),
            'warehouse_id' => (new WarehouseService())->getWarehouseRandomByCountry(LocateConstant::COUNTRY_VI)->id,
            'weight' => $weight,
            // 'volume' => $volume,
            'delivery_cost' => $array[0],
            'china_shipping_cost' => $array[1],
            'international_shipping_cost' => $array[2],
            'inspection_cost' => $isInspection ? $array[3] : 0,
            'is_inspection' => $isInspection,
            'insurance_cost' => $isInsurance ? $array[4] : 0,
            'is_insurance' => $isInsurance,
            'woodworking_cost' => $isWoodworking ? $array[5] : 0,
            'is_woodworking' => $isWoodworking,
            'note' => $this->faker->paragraphs(2, true),
            'status' => PackageConstant::STATUS_PENDING,
            'description' => $this->faker->paragraphs(2, true),
        ];
    }
}
