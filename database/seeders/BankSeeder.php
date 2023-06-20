<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = json_decode(file_get_contents(database_path('mocks/vietnam-banks.json')), true);
        $data =[
            [
                'id' => getUuid(),
                'name' => '中国工商银行',
                'short_name' => '中国工商银行',
                'bank_id' => 1,
                'created_at' => now(),
                'country' => 'cn',
            ],
            [
                'id' => getUuid(),
                'name' => '中国农业银行',
                'short_name' => '中国农业银行',
                'bank_id' => 2,
                'created_at' => now(),
                'country' => 'cn',
            ],
            [
                'id' => getUuid(),
                'name' => '中国建设银行',
                'short_name' => '中国建设银行',
                'bank_id' => 3,
                'created_at' => now(),
                'country' => 'cn',
            ],
            [
                'id' => getUuid(),
                'name' => '中国银行',
                'short_name' => '中国银行',
                'bank_id' => 4,
                'created_at' => now(),
                'country' => 'cn',
            ],
        ];
        foreach ($banks as $bank) {
            $short = explode(',', $bank['shortName']);
            $data[] = [
                'id' => getUuid(),
                'name' => $bank['vn_name'],
                'short_name' => array_shift($short),
                'bank_id' => $bank['bankId'],
                'created_at' => now(),
                'country' => 'vi',
            ];
        }
        Bank::query()->truncate();
        Bank::query()->insert($data);
    }
}
