<?php

namespace Database\Seeders;

use App\Models\SupplierType;
use Illuminate\Database\Seeder;

class SupplierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupplierType::query()->truncate();
        SupplierType::query()->insert(
            [
                [
                    'id' => getUuid(),
                    'name' => 'Số điện thoại',
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Email',
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Wechat',
                ],
                [
                    'id' => getUuid(),
                    'name' => 'QQ',
                ],
            ]
        );
    }
}
