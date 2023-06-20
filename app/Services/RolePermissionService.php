<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;

class RolePermissionService implements Service
{
    /**
     * assignPermissionToRole: Assign Permission to Role
     * @param array $permissions
     * @param string $roleId
     * @return void
     */
    public function assignPermissionToRole(array $permissions, string $roleId): void
    {
        if(count($permissions) > 0) {
            foreach($permissions as $permission) {
                $p = Permission::where('name', $permission)->first();
                RolePermission::create(
                    [
                        "role_id" => $roleId,
                        "permission_id" => $p->id,
                    ]
                );
            }
        }
    }

    /**
     * getAllPermissionOfRole: Get All Permission Of Role
     * @param string $roleId
     * @return array
     */
    public function getAllPermissionOfRole(string $roleId): array
    {
        $permission = [];
        $roleHasPermission = RolePermission::with('permissions')->where('role_id', $roleId)->get();
        foreach ($roleHasPermission as $role) {
            $permission[] = $role->permissions[0];
        }
        return $permission;
    }
}
