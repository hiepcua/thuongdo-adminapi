<?php

namespace Database\Factories;

use App\Constants\ConfigConstant;
use App\Constants\CustomerConstant;
use App\Constants\LocateConstant;
use App\Constants\TimeConstant;
use App\Models\Customer;
use App\Services\ConfigService;
use App\Services\CustomerDeliveryService;
use App\Services\CustomerService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        /** @var Customer $customer */
        $customer = (new CustomerService())->getCustomerTest();
        return [
            "code" => $customer->code . Str::random(10),
            "delivery_type" => $this->faker->randomElement(['normal', 'fast']),
            "customer_id" => (new CustomerService())->getCustomerTest()->id,
            "warehouse_id" => (new WarehouseService())->getWarehouseRandomByCountry(LocateConstant::COUNTRY_VI)->id,
            "customer_delivery_id" => (new CustomerDeliveryService())->getDeliveryRandomByCustomer()->id,
            "supplier_id" => (new SupplierService())->getSupplierRandomByOrganization($customer->organization_id),
            'exchange_rate' => (new ConfigService())->getExchangeRate(),
            'date_ordered' => date(TimeConstant::DATETIME)
        ];
    }
}
