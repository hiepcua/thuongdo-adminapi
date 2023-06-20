<?php

namespace Database\Seeders;

use App\Constants\DepartmentConstant;
use App\Models\Department;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Constants\RoleConstant;
use App\Models\Organization;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Department::query()->truncate();
        $department1 = Department::query()->create(
            [
                'name' => DepartmentConstant::CARE_NAME,
                'organization_id' => optional(Organization::query()->first())->id
            ]
        );
        $department1->assignRole(Role::query()->where('name', RoleConstant::CARE_ROLE)->first());

        $department2 = Department::query()->create(
            [
                'name' => DepartmentConstant::COMPLAIN_NAME,
                'organization_id' => optional(Organization::query()->first())->id
            ]
        );
        $department2->assignRole(Role::query()->where('name', RoleConstant::COMPLAIN_ROLE)->first());
    }
}
