<?php

namespace Database\Factories;

use App\Helpers\RandomHelper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name,
            'address' => $this->faker->address,
            'code' => 'TC_'.Str::random(13),
            'avatar' => $this->faker->image,
            'representative_name' => $this->faker->unique()->name,
            'representative_phone' => RandomHelper::phoneNumber(),
            'domain' => $this->faker->unique()->url,
        ];
    }
}
