<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Organization;
use App\Models\OrganizationBank;
use App\Models\ReportComplain;
use App\Models\ReportDelivery;
use App\Models\ReportFine;
use App\Services\OrganizationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::query()->truncate();
        /** @var Organization $organization */
        $organization = Organization::query()->create(
            [
                'name' => 'CÔNG TY CỔ PHẦN QUỐC TẾ THƯƠNG ĐÔ',
                'address' => 'Số 14 Nguyễn Viết Xuân, Thanh Xuân, Hà Nội',
                'email' => 'thuongdo@gmail.com',
                'phone_number' => '19006825- 0473005999',
                'code' => 'TC_0001',
                'avatar' => '/storage/organization/thuongdo.png',
                'domain' => '*',
                'tax_code' => '0107464325'
            ]
        );
        $id = $organization->id;
        $data = ['organization_id' => $id];
        ReportDelivery::query()->firstOrCreate($data);
        ReportComplain::query()->firstOrCreate($data);
        ReportFine::query()->firstOrCreate($data);
        OrganizationBank::query()->insert(
            [
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970436")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '0931004212231',
                    'created_at' => now()
                ],
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970418")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '12210002124728',
                    'created_at' => now()
                ],
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970415")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '106872053549',
                    'created_at' => now()
                ],
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970405")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '1505205667635',
                    'created_at' => now()
                ],
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970407")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '19036197572014',
                    'created_at' => now()
                ],
                [
                    'id' => getUuid(),
                    'bank_id' => Bank::query()->where('bank_id', "970403")->first()->id,
                    'organization_id' => $id,
                    'name' => 'VU MINH KHUONG',
                    'account_number' => '020088304811',
                    'created_at' => now()
                ]
            ]
        );
        (new OrganizationService())->addReportLevel($organization->id);
    }
}
