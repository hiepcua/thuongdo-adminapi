<?php

namespace Database\Factories;

use App\Constants\CustomerConstant;
use App\Constants\LocateConstant;
use App\Models\Customer;
use App\Services\CustomerDeliveryService;
use App\Services\CustomerService;
use App\Services\SupplierService;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
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
            'supplier_id' => (new SupplierService())->getSupplierRandomByOrganization($customer->organization_id),
            'customer_id' => $customer->id,
            'warehouse_id' => (new WarehouseService())->getWarehouseRandomByCountry(LocateConstant::COUNTRY_VI)->id,
            'customer_delivery_id' => (new CustomerDeliveryService())->getDeliveryRandomByCustomer()->id,
        ];
    }
}
