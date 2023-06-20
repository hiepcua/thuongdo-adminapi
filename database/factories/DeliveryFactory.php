<?php

namespace Database\Factories;

use App\Models\CustomerDelivery;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'organization_id' => Organization::query()->first()->id,
            'receiver' => CustomerDelivery::query()->first()->id,
        ];
    }
}
