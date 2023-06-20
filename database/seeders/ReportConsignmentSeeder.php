<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\ReportConsignment;
use App\Services\CustomerService;
use Illuminate\Database\Seeder;

class ReportConsignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReportConsignment::query()->truncate();
        ReportConsignment::query()->create(
            [
                'organization_id' => Organization::query()->first()->id,
                'customer_id' => (new CustomerService())->getCustomerTest()->id
            ]
        );
    }
}
