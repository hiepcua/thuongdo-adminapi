<?php

namespace Database\Seeders;

use App\Models\SupplierBank;
use Illuminate\Database\Seeder;

class SupplierBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupplierBank::query()->truncate();
        SupplierBank::factory(4)->create();
    }
}
