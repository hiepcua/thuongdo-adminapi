<?php

namespace Database\Seeders;

use App\Constants\RoleConstant;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Role::query()->truncate();
        Permission::query()->truncate();
        Module::query()->truncate();
        Schema::enableForeignKeyConstraints();
        $staff = 'Nhân viên';
        $customer = 'Khách hàng';
        $system = 'Quản trị';
        $department = 'Phòng ban';
        Module::query()->insert(
            [
                [
                    'id' => getUuid(),
                    'name' => $system,
                    'code' => 'administrator'
                ],
                [
                    'id' => getUuid(),
                    'name' => $department,
                    'code' => 'administrator-department'
                ],
                [
                    'id' => getUuid(),
                    'name' => $staff,
                    'code' => 'administrator-user'
                ],
                [
                    'id' => getUuid(),
                    'name' => $customer,
                    'code' => 'administrator-customer'
                ],
            ]
        );

        $userUpdateUuid = getUuid();
        $userDeleteUuid = getUuid();
        $userCreateUuid = getUuid();
        $userListUuid = getUuid();

        $departmentUpdateUuid = getUuid();
        $departmentDeleteUuid = getUuid();
        $departmentCreateUuid = getUuid();
        $departmentListUuid = getUuid();

        $organizationUpdateUuid = getUuid();
        $organizationDeleteUuid = getUuid();
        $organizationCreateUuid = getUuid();
        $organizationListUuid = getUuid();

        $roleUpdateUuid = getUuid();
        $roleDeleteUuid = getUuid();
        $roleCreateUuid = getUuid();
        $roleListUuid = getUuid();

        DB::table('permissions')->insert(
            [
                [
                    'id' => $departmentCreateUuid,
                    'name' => 'create-department',
                    'guard_name' => 'api',
                    'display_name' => 'Tạo phòng ban',
                    'module_id' => $this->getModule($department),
                    'is_show_department' => false
                ],
                [
                    'id' => $departmentUpdateUuid,
                    'name' => 'update-department',
                    'guard_name' => 'api',
                    'display_name' => 'Chỉnh sửa phòng ban',
                    'module_id' => $this->getModule($department),
                    'is_show_department' => false
                ],
                [
                    'id' => $departmentDeleteUuid,
                    'name' => 'delete-department',
                    'guard_name' => 'api',
                    'display_name' => 'Xoá phòng ban',
                    'module_id' => $this->getModule($department),
                    'is_show_department' => false
                ],
                [
                    'id' => $departmentListUuid,
                    'name' => 'list-department',
                    'guard_name' => 'api',
                    'display_name' => 'Danh sách phòng ban',
                    'module_id' => $this->getModule($department),
                    'is_show_department' => false
                ],
                [
                    'id' => $userCreateUuid,
                    'name' => 'create-user',
                    'guard_name' => 'api',
                    'display_name' => 'Tạo người dùng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => false
                ],
                [
                    'id' => $userUpdateUuid,
                    'name' => 'update-user',
                    'guard_name' => 'api',
                    'display_name' => 'Chỉnh sửa người dùng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => false
                ],
                [
                    'id' => $userDeleteUuid,
                    'name' => 'delete-user',
                    'guard_name' => 'api',
                    'display_name' => 'Xoá người dùng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => false
                ],
                [
                    'id' => $userListUuid,
                    'name' => 'list-user',
                    'guard_name' => 'api',
                    'display_name' => 'Danh sách người dùng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => false
                ],
                [
                    'id' => $organizationCreateUuid,
                    'name' => 'create-organization',
                    'guard_name' => 'api',
                    'display_name' => 'Tạo tổ chức',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $organizationUpdateUuid,
                    'name' => 'update-organization',
                    'guard_name' => 'api',
                    'display_name' => 'Chỉnh sửa tổ chức',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $organizationDeleteUuid,
                    'name' => 'delete-organization',
                    'guard_name' => 'api',
                    'display_name' => 'Xoá tổ chức',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $organizationListUuid,
                    'name' => 'list-organization',
                    'guard_name' => 'api',
                    'display_name' => 'Danh sách tổ chức',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $roleCreateUuid,
                    'name' => 'create-role',
                    'guard_name' => 'api',
                    'display_name' => 'Tạo vai trò',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $roleUpdateUuid,
                    'name' => 'update-role',
                    'guard_name' => 'api',
                    'display_name' => 'Chỉnh sửa vao trò',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $roleDeleteUuid,
                    'name' => 'delete-role',
                    'guard_name' => 'api',
                    'display_name' => 'Xoá vai trò',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $roleListUuid,
                    'name' => 'list-role',
                    'guard_name' => 'api',
                    'display_name' => 'Danh sách vai trò',
                    'module_id' => $this->getModule($system),
                    'is_show_department' => false
                ],
                [
                    'id' => $permissionCustomerConsulting = getUuid(),
                    'name' => RoleConstant::PERMISSION_CUSTOMER_CONSULTING,
                    'guard_name' => 'api',
                    'display_name' => 'Tư vấn khách hàng mới',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => true
                ],
                [
                    'id' => $permissionTakeCareCustomer = getUuid(),
                    'name' => RoleConstant::PERMISSION_TAKE_CARE_CUSTOMER,
                    'guard_name' => 'api',
                    'display_name' => 'Chăm sóc khách hàng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => true
                ],
                [
                    'id' => $permissionQuotationCustomer = getUuid(),
                    'name' => RoleConstant::PERMISSION_QUOTE_CUSTOMER,
                    'guard_name' => 'api',
                    'display_name' => 'Báo giá đơn hàng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => true
                ],
                [
                    'id' => $permissionOrderStaff = getUuid(),
                    'name' => RoleConstant::PERMISSION_ORDER_STAFF,
                    'guard_name' => 'api',
                    'display_name' => 'Đặt hàng',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => true
                ],
                [
                    'id' => $permissionComplainStaff = getUuid(),
                    'name' => RoleConstant::PERMISSION_COMPLAIN_STAFF,
                    'guard_name' => 'api',
                    'display_name' => 'Khiếu nại',
                    'module_id' => $this->getModule($staff),
                    'is_show_department' => true
                ]
            ]
        );
        $complainUuid = getUuid();
        $customerUuid = getUuid();
        $adminUuid = getUuid();
        $systemAdminUuid = getUuid();
        DB::table('roles')->insert(
            [
                [
                    'id' => $customerUuid,
                    'name' => 'khach-hang',
                    'display_name' => 'Khách Hàng',
                    'description' => 'Các chức năng liên quan đến khách hàng.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => getUuid(),
                    'name' => RoleConstant::CARE_ROLE,
                    'display_name' => 'Chăm Sóc',
                    'description' => 'Các chức năng liên quan đến chăm sóc khách hàng.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => getUuid(),
                    'name' => RoleConstant::COUNSELOR_ROLE,
                    'display_name' => 'Tư Vấn',
                    'description' => 'Các chức năng liên quan đến tư vấn khách hàng.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => getUuid(),
                    'name' => RoleConstant::ORDER_ROLE,
                    'display_name' => 'Đặt Hàng',
                    'description' => 'Các chức năng liên quan đến đặt hàng.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => $complainUuid,
                    'name' => RoleConstant::COMPLAIN_ROLE,
                    'display_name' => 'Khiếu Nại',
                    'description' => 'Các chức năng liên quan đến khiếu nại.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => $adminUuid,
                    'name' => 'admin',
                    'display_name' => 'Admin',
                    'description' => 'Các chức năng liên quan đến quản trị.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
                [
                    'id' => $systemAdminUuid,
                    'name' => 'system-admin',
                    'display_name' => 'System Admin',
                    'description' => 'Các chức năng liên quan đến quản trị hệ thống.',
                    'guard_name' => 'api',
                    'is_department' => false
                ],
            ]
        );
        DB::table('role_has_permissions')->insert(
            [
                [
                    'permission_id' => $userCreateUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $userUpdateUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $userDeleteUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $userListUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $departmentCreateUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $departmentUpdateUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $departmentDeleteUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $departmentListUuid,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $permissionCustomerConsulting,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $permissionOrderStaff,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $permissionComplainStaff,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $permissionQuotationCustomer,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $permissionTakeCareCustomer,
                    'role_id' => $adminUuid,
                ],
                [
                    'permission_id' => $organizationCreateUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $organizationUpdateUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $organizationDeleteUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $organizationListUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $roleCreateUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $roleUpdateUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $roleDeleteUuid,
                    'role_id' => $systemAdminUuid,
                ],
                [
                    'permission_id' => $roleListUuid,
                    'role_id' => $systemAdminUuid,
                ],
            ]
        );
    }

    /**
     * @param  string  $name
     * @return string
     */
    public function getModule(string $name): string
    {
        return optional(Module::query()->where('name', $name)->first())->id;
    }
}
