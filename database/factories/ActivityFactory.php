<?php

namespace Database\Factories;

use App\Constants\CustomerConstant;
use App\Models\Customer;
use App\Models\Order;
use App\Services\CustomerService;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $customer = (new CustomerService())->getCustomerByEmail(
            CustomerConstant::CUSTOMER_TEST
        );
        return [
            'subject_type' => Order::class,
            'subject_id' => Order::factory(),
            'causer_type' => Customer::class,
            'causer_id' => $customer->id,
            'log_name' => 'order_log',
            'content' => $this->faker->randomElement(['Chờ đặt cọc', 'Đã đặt cọc', 'Tạo đơn hàng']),
            'organization_id' => $customer->organization_id
        ];
    }
}
