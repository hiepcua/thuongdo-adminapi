<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->words(3, true);
        preg_match_all('/(?<=\b)\w/iu', $name, $matches);
        $code = mb_strtoupper(implode('', $matches[0]));
        return [
            'id' => getUuid(),
            'code' => 'KHO-'.$code,
            'name' => 'Kho '.$name,
            'address' => $this->faker->address,
        ];
    }
}
