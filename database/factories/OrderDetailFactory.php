<?php

namespace Database\Factories;

use App\Constants\CustomerConstant;
use App\Helpers\AccountingHelper;
use App\Services\CategoryService;
use App\Services\CustomerService;
use App\Services\SupplierService;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $customer = (new CustomerService())->getCustomerTest();
        return [
            "name" => $this->faker->name,
            "link" => $this->faker->url,
            "image" => '/default.png',
            "note" => $this->faker->paragraph(1),
            "unit_price_cny" => $price = $this->faker->numberBetween(1, 20),
            "quantity" => $quantity = $this->faker->numberBetween(1, 10),
            'amount_cny' => AccountingHelper::getCosts($price * $quantity),
            "supplier_id" => (new SupplierService())->getSupplierRandomByOrganization($customer->organization_id),
            "category_id" => (new CategoryService())->getCategoryRandomByOrganization($customer->organization_id),
        ];
    }
}
