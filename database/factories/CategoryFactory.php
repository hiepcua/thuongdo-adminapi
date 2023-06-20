<?php

namespace Database\Factories;

use App\Constants\CustomerConstant;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'organization_id' => (new CustomerService())->getCustomerTest()->organization_id
        ];
    }
}
