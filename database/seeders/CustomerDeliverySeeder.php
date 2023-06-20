<?php

namespace Database\Seeders;

use App\Models\CustomerDelivery;
use App\Services\CustomerService;
use Illuminate\Database\Seeder;

class CustomerDeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerDelivery::query()->truncate();
        CustomerDelivery::query()->create(
            [
                'customer_id' => (new CustomerService())->getCustomerTest()->id,
                'receiver' => 'Customer',
                'address' => '102 Cầu Giấy',
                'phone_number' => '0909090909',
                'ward_id' => "25b8048e-139b-47e1-8df8-6ef81f9dfba3",
                'district_id' => "069c2f34-3949-4f71-9457-1c1f41b02bd6",
                'province_id' => '5c8c099c-e7ad-4351-8b09-d2df15f2a068',
                'is_default' => 1
            ]
        );
    }
}
