<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class LocateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = json_decode(file_get_contents(database_path('mocks/locates/provinces.json')), true);
        $districts = json_decode(file_get_contents(database_path('mocks/locates/districts.json')), true);
        $wards = json_decode(file_get_contents(database_path('mocks/locates/wards.json')), true);

        Province::query()->truncate();
        Province::query()->insert($provinces);

        District::query()->truncate();
        District::query()->insert($districts);

        Ward::query()->truncate();
        $chunks = array_chunk($wards, 1000);
        foreach ($chunks as $chunk){
            Ward::query()->insert($chunk);
        }
        $a = http_build_query([]);
        unset($processDistricts, $provinces, $districts, $wards, $chunks);
    }
}
