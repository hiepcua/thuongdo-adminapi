<?php

namespace Database\Seeders;

use App\Models\OrderPackage;
use Illuminate\Database\Seeder;

class OrderPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderPackage::query()->truncate();
    }
}
