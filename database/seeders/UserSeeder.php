<?php

namespace Database\Seeders;

use App\Constants\DepartmentConstant;
use App\Constants\RoleConstant;
use App\Constants\UserConstant;
use App\Helpers\RandomHelper;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Staff;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = '123456';
        Staff::query()->truncate();
        $systemAdmin = Staff::query()->withoutGlobalScope(OrganizationScope::class)->create(
            [
                'id' => getUuid(),
                'name' => 'System Administrator',
                'email' => UserConstant::SYSTEM_ADMIN_EMAIL,
                'code' => RandomHelper::userCode(),
                'password' => Hash::make($password),
                'department_id' => null,
                'organization_id' => null,
            ]
        );
        $systemAdmin->assignRole(Role::query()->where('name',  RoleConstant::SYSTEM_ADMIN_ROLE)->first());
        Staff::factory(1)->create(
            [
                'name' => 'Administrator',
                'email' => UserConstant::ADMIN_EMAIL,
                'code' => RandomHelper::userCode(),
                'department_id' => null,
                'organization_id' => optional(Organization::query()->inRandomOrder()->first())->id
            ]
        )->transform(function($user, $key) {
            $user->assignRole(Role::query()->where('name',  RoleConstant::ADMIN_ROLE)->first());
        });
        Staff::factory(rand(4, 9))->create(
            [
                'code' => RandomHelper::userCode(),
                'department_id' => optional(
                    Department::query()->where('name', DepartmentConstant::CARE_NAME)->first()
                )->id,
                'organization_id' => optional(Organization::query()->inRandomOrder()->first())->id
            ]
        )->transform(function($user, $key) {
            $user->assignRole(Role::query()->where('name', $key % 2 == 0 ? RoleConstant::CARE_ROLE : RoleConstant::COMPLAIN_ROLE)->first());
        });
        Staff::factory(1)->create(
            [
                'name' => 'NV chăm sóc',
                'code' => RandomHelper::userCode(),
                'email' => 'care_staff@thuongdo.com',
                'department_id' => optional(
                    Department::query()->where('name', DepartmentConstant::CARE_NAME)->first()
                )->id,
                'organization_id' => optional(Organization::query()->inRandomOrder()->first())->id
            ]
        )->transform(function($user) {
            $user->assignRole(Role::query()->where('name', RoleConstant::CARE_ROLE)->first());
        });
        Staff::factory(1)->create(
            [
                'name' => 'NV khiếu nại',
                'code' => RandomHelper::userCode(),
                'email' => 'complain_staff@thuongdo.com',
                'department_id' => optional(
                    Department::query()->where('name', DepartmentConstant::CARE_NAME)->first()
                )->id,
                'organization_id' => optional(Organization::query()->inRandomOrder()->first())->id
            ]
        )->transform(function($user) {
            $user->assignRole(Role::query()->where('name', RoleConstant::COMPLAIN_ROLE)->first());
        });
    }
}
