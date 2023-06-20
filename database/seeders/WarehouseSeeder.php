<?php

namespace Database\Seeders;

use App\Constants\LocateConstant;
use App\Models\District;
use App\Models\Organization;
use App\Models\Province;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::query()->truncate();
        $hanoi = Province::query()->where('name', 'Hà Nội')->first()->id;
        $hcm = Province::query()->where('name', 'Hồ Chí Minh')->first()->id;
        $qt = Province::query()->where('name', 'Quảng Tây')->first()->id;
        $qd = Province::query()->where('name', 'Quảng Đông')->first()->id;
        $vi = LocateConstant::COUNTRY_VI;
        $cn = LocateConstant::COUNTRY_CN;
        $organization = optional(Organization::query()->first())->id;
        Warehouse::query()->insert(
            [
                [
                    'id' => getUuid(),
                    'code' => 'VI-HN-TX',
                    'name' => 'Kho Trần Điền',
                    'district_id' => District::query()->where('ghn_id', 1493)->first()->id,
                    'province_id' => $hanoi,
                    'organization_id' => $organization,
                    'address' => '298 Trần Điền, Định Công, Thanh Xuân',
                    'country' => $vi
                ],
                [
                    'id' => getUuid(),
                    'code' => 'VI-HN-HBT',
                    'name' => 'Kho Tạ Quang Bửu',
                    'district_id' => District::query()->where('ghn_id', 1488)->first()->id,
                    'province_id' => $hanoi,
                    'organization_id' => $organization,
                    'address' => '107 E2 ngõ 27 phố Tạ Quang Bửu,Phường Bách Khoa, Hai Bà Trưng',
                    'country' => $vi
                ],
                [
                    'id' => getUuid(),
                    'code' => 'VI-HN-HD',
                    'name' => 'Kho Văn Quán',
                    'district_id' => District::query()->where('ghn_id', 1542)->first()->id,
                    'province_id' => $hanoi,
                    'organization_id' => $hanoi,
                    'address' => '21 Phố Văn Quán, P.Văn Quán, Hà Đông',
                    'country' => $vi
                ],
                [
                    'id' => getUuid(),
                    'code' => 'VI-HCM-Q6',
                    'name' => 'Kho Nguyễn Văn Luông',
                    'district_id' => District::query()->where('ghn_id', 1448)->first()->id,
                    'province_id' => $hcm,
                    'organization_id' => $organization,
                    'address' => '47 Nguyễn Văn Luông, Quận 6',
                    'country' => $vi
                ],
                [
                    'id' => getUuid(),
                    'code' => 'CN-QD-QC',
                    'name' => 'Kho Quảng Châu',
                    'district_id' => null,
                    'province_id' => $qd,
                    'organization_id' => $organization,
                    'address' => '广东省 广州市 白云区 石井街 石庆路74号8号仓 094918',
                    'country' => $cn
                ],
                [
                    'id' => getUuid(),
                    'code' => 'CN-QT-DH',
                    'name' => 'Kho Đông Hưng',
                    'district_id' => null,
                    'province_id' => $qt,
                    'organization_id' => $organization,
                    'address' => '广西壮族自治区 防城港市 东兴市 东兴镇 冲卜一路65-1号094918仓库',
                    'country' => $cn
                ]
            ]
        );
    }
}
