<?php

namespace App\Services;

use App\Constants\CustomerConstant;
use App\Constants\RoleConstant;
use App\Constants\UserConstant;
use App\Helpers\RandomHelper;
use App\Models\Department;
use App\Models\Organization;
use App\Models\ReportLevel;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class OrganizationService
 * @package App\Services
 */
class OrganizationService extends BaseService
{

    public function store(array $data): JsonResponse
    {
        $res = DB::Transaction(function () use ($data) {
            /** @var Organization $organization */
            $organization = Organization::query()->create($data);
            // Create User Admin for organization
            $data['code'] = RandomHelper::userCode();
            // create User
            $data['password'] = Hash::make(UserConstant::PASSWORD_DEFAULT);
            $data['organization_id'] = optional($organization)->id;
            $staff = Staff::query()->create($data);
            // assign role admin for User
            $staff->assignRole(Role::query()->where('name', RoleConstant::ADMIN_ROLE)->first());
            $this->addReportLevel($organization->id);
            return $organization;
        });
        return resSuccessWithinData($res);
    }

    /**
     * @param string $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(string $id): JsonResponse
    {
        $departments = Department::query()->where('organization_id', $id)->get();
        if (count($departments) > 0) {
            throw new \Exception(trans('organization.organization_has_department'));
        }
        return parent::destroy($id);
    }

    public function addReportLevel(string $organization)
    {
        $data = [];
        foreach (CustomerConstant::CUSTOMER_LEVEL as $key => $name) {
            $data[] = [
                'id' => getUuid(),
                'organization_id' => $organization,
                'level' => $key,
                'name' => $name
            ];
        }
        ReportLevel::query()->insert($data);
    }

    public function getOrganizationByDomain(string $domain): string
    {
        return optional(Organization::query()->where('domain', $domain)->first())->id ?? $this->getOrganizationDefault(
            )->id;
    }

    public function getOrganizationDefault(): string
    {
        return $this->getOrganizationByDomain('*');
    }
}
