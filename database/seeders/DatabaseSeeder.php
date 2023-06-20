<?php

namespace Database\Seeders;

use Database\Factories\ContactMethodFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                CustomerReasonClosingSeeder::class,
                BankSeeder::class,
                OrganizationSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                LabelSeeder::class,
                CustomerSeeder::class,
                CustomerDeliverySeeder::class,
                LocateSeeder::class,
                WarehouseSeeder::class,
                SupplierSeeder::class,
                CartSeeder::class,
                ConfigSeeder::class,
                CategorySeeder::class,
                OrderSeeder::class,
                ActivitySeeder::class,
                ComplainSeeder::class,
                TransporterSeeder::class,
                ReportConsignmentSeeder::class,
                DepartmentSeeder::class,
                ContactMethodSeeder::class,
                SupplierBankSeeder::class,
                SupplierTypeSeeder::class,
            ]
        );
    }
}
