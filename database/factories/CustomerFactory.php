<?php

namespace Database\Factories;

use App\Helpers\RandomHelper;
use App\Models\Label;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'code' => 'KH'.Str::random(12),
            'password' => Hash::make('123456'),
            'phone_number' => RandomHelper::phoneNumber(),
            'organization_id' => optional(Organization::query()->inRandomOrder()->first())->id,
            'label_id' => optional(Label::query()->inRandomOrder()->first())->id,
            'level' => 1
        ];
    }
}
