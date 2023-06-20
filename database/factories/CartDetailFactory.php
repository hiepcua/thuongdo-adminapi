<?php

namespace Database\Factories;

use App\Constants\CustomerConstant;
use App\Models\Cart;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $customerId = (new CustomerService())->getCustomerTest()->id;
        $unitPrice = $this->faker->randomFloat();
        $quantity = random_int(1, 10);
        return [
            'cart_id' => Cart::query()->where('customer_id', $customerId)->inRandomOrder()->first()->id,
            'name' => implode(' ', $this->faker->words(10)),
            'link' => $this->faker->url,
            'image' => $this->faker->imageUrl,
            'unit_price_cny' => $unitPrice,
            'amount_cny' => $unitPrice * $quantity,
            'quantity' => $quantity
        ];
    }
}
