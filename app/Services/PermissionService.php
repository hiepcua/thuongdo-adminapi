<?php

namespace App\Services;

use App\Http\Resources\Module\ModuleResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\Permission\PermissionResource;
use App\Models\Department;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;

/**
 * Class PermissionService
 * @package App\Services
 */
class PermissionService extends BaseService
{

    /**
     *  getModulePermission: List All Module Permission
     *
     * @return JsonResponse
     */
    public function getModulePermission(): JsonResponse
    {
        $record = Module::query()->where('name', '!=', "Quản trị")->get();
        return resSuccessWithinData(new ListResource($record, ModuleResource::class));
    }

    public function getUserPermission(Staff $user): ListResource
    {
        $permissions = [];
        if(isset($user->department_id)) {
            $department = Department::with('roles')->findOrFail($user->department_id);
            $permissionsOfDepartment =
                count($department->roles) > 0 ?
                    (new RolePermissionService)->getAllPermissionOfRole($department->roles[0]->pivot->role_id)
                    : [];
            $permissions = array_merge($permissions, $permissionsOfDepartment);
        }
        $permissionsOfUser =
            count($user->roles) > 0 ?
                (new RolePermissionService)->getAllPermissionOfRole($user->roles[0]->pivot->role_id)
                : [];
        $permissions = array_merge($permissions, $permissionsOfUser);
        return (new ListResource($permissions, PermissionResource::class));
    }
}
