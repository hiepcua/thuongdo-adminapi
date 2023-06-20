<?php

namespace Database\Seeders;

use App\Constants\CustomerConstant;
use App\Helpers\RandomHelper;
use App\Models\Customer;
use App\Models\Label;
use App\Models\Organization;
use App\Models\ReportCustomer;
use App\Models\ReportLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::query()->truncate();
        $organizationId = Organization::query()->first()->id;
        $customerId = getUuid();
        $tungNTId = getUuid();
        Customer::query()->insert(
            [
                [
                    'id' => $customerId,
                    'name' => 'Customer',
                    'email' => CustomerConstant::CUSTOMER_TEST,
                    'code' => 'KH_'.date('Ym').'001',
                    'password' => Hash::make('123456'),
                    'phone_number' => RandomHelper::phoneNumber(),
                    'organization_id' => $organizationId,
                    'label_id' => optional(Label::query()->inRandomOrder()->first())->id,
                    'created_at' => now()
                ],
                [
                    'id' => $tungNTId,
                    'name' => 'Tùng Nguyễn',
                    'email' => 'tungnt@gmail.com',
                    'code' => 'KH_'.date('Ym').'002',
                    'password' => Hash::make('Admin@123'),
                    'phone_number' => RandomHelper::phoneNumber(),
                    'organization_id' => $organizationId,
                    'label_id' => optional(Label::query()->inRandomOrder()->first())->id,
                    'created_at' => now()
                ]
            ]
        );
        ReportCustomer::query()->firstOrCreate(['customer_id' => $customerId]);
        ReportCustomer::query()->firstOrCreate(['customer_id' => $tungNTId]);
        ReportLevel::query()->where(['organization_id' => $organizationId, 'level' => 0])->increment('quantity', 2);
    }
}
