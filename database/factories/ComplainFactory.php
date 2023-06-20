<?php

namespace Database\Factories;

use App\Models\ComplainType;
use App\Models\Order;
use App\Models\Solution;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => Order::query()->first()->id,
            'complain_type_id' => ComplainType::query()->inRandomOrder()->first()->id,
            'solution_id' => Solution::query()->inRandomOrder()->first()->id,
        ];
    }
}
