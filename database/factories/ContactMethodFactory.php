<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\SupplierService;
use App\Services\CustomerService;

class ContactMethodFactory extends Factory
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
            'name' => $this->faker->name,
            "supplier_id" => (new SupplierService())->getSupplierRandomByOrganization($customer->organization_id),
        ];
    }
}
