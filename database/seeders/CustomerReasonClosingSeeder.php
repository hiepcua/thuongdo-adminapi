<?php

namespace Database\Seeders;

use App\Models\CustomerReasonInactive;
use Illuminate\Database\Seeder;

class CustomerReasonClosingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CustomerReasonInactive::query()->truncate();
        $data = ['Tài khoản ảo', 'Khách không có nhu cầu', 'Khách hàng đặt hàng cấm', 'Trùng tài khoản'];
        $result = [];
        foreach ($data as $item) {
            $result[] = [
                'id' => getUuid(),
                'name' => $item
            ];
        }
        CustomerReasonInactive::query()->insert($result);
    }
}
