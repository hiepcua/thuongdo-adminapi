<?php

namespace Database\Factories;

use App\Services\CategoryService;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupplierFactory extends Factory
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
            'name' => $this->faker->name,
            'logo' => 'https://cf.shopee.vn/file/ca0429429ff9ae1fc6fd779e81f4e82a',
            'order_amount' => rand(),
            'complain_number' => 15,
            'website' => 'https://shop397956688.taobao.com/index.htm?spm=2013.1.w5002-24258331278.2.1fca32a15SYFzy',
            'type' => $this->faker->randomElement(['online', 'offline']),
            'code' => 'CC_' . Str::random(12),
            'customer_id' => (new CustomerService())->getCustomerTest()->id,
            'industry' => (new CategoryService())->getCategoryRandomByOrganization($customer->organization_id),
            'organization_id' => $customer->organization_id,
        ];
    }
}
