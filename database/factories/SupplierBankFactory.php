<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\SupplierService;
use App\Services\CustomerService;

class SupplierBankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = (new CustomerService())->getCustomerTest();
        return [
            'account_holder' => $this->faker->name,
            'account_number' => rand(),
            'bank_id' => rand(),
            "supplier_id" => (new SupplierService())->getSupplierRandomByOrganization($customer->organization_id),
        ];
    }
}
