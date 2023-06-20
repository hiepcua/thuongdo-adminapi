<?php


namespace App\Services;
use App\Constants\RoleConstant;
use App\Helpers\RandomHelper;
use App\Http\Resources\User\UserResource;
use App\Models\Department;
use App\Models\ReportUserComplainCustomer;
use App\Models\ReportUserCounselingCustomer;
use App\Models\ReportUserOrderedCustomer;
use App\Models\ReportUserQuotationCustomer;
use App\Models\ReportUserTakeCareCustomer;
use App\Models\Role;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class StaffService extends BaseService
{
    protected string $_resource = UserResource::class;

    public function store(array $data): JsonResponse
    {
        // create random User code
        $data['code'] = RandomHelper::userCode();
        // create User
        $data['password'] = Hash::make($data['password']);
        $record = Staff::query()->create($data);
        // assign roles for User
        if(isset($data['role_id'])) {
            $record->assignRole(Role::query()->where('id', $data['role_id'])->first());
        }
        (new StaffService())->permissionToggle($data['department_id']);
        return resSuccessWithinData($record);
    }

    public function update(string $id, array $data)
    {
        $data = (new UserService())->updatePassword($data);
        $record = parent::update($id, $data);
        // update roles
        if(isset($data['role_id']) && $record != null) {
            $record->syncRoles([Role::query()->where('id', $data['role_id'])->first()]);
        }
        (new StaffService())->permissionToggle($data['department_id']);
        return $record;
    }

    /**
     * @param  string  $role
     * @return mixed
     */
    public function getStaffByRole(string $role)
    {
        return Staff::query()->role($role)->select('id', 'name')->get();
    }

    public function getStaffCounselor()
    {
        $userIds = ReportUserTakeCareCustomer::query()->where('status', 1)->pluck('user_id');
        return Staff::query()->select('id', 'name')->find($userIds);
    }
    /**
     * @param  string  $departmentId
     * @param  bool  $isActive
     */
    public function permissionToggle(string $departmentId): void
    {
        $department = Department::query()->findOrFail($departmentId);
        $permissions = $department->getAllPermissions();
        $isActive = $permissions->where('name', RoleConstant::PERMISSION_TAKE_CARE_CUSTOMER)->isNotEmpty();
        $isActiveQuotation = $permissions->where('name', RoleConstant::PERMISSION_QUOTE_CUSTOMER)->isNotEmpty();
        $isActiveOrder = $permissions->where('name', RoleConstant::PERMISSION_ORDER_STAFF)->isNotEmpty();
        $isActiveCounselor = $permissions->where('name', RoleConstant::PERMISSION_CUSTOMER_CONSULTING)->isNotEmpty();
        $isActiveComplain = $permissions->where('name', RoleConstant::PERMISSION_COMPLAIN_STAFF)->isNotEmpty();
        Staff::query()->where('department_id', $departmentId)->each(
            function ($user) use (
                $isActive,
                $isActiveQuotation,
                $isActiveOrder,
                $isActiveCounselor,
                $isActiveComplain,
                $permissions
            ) {
                /** @var ReportUserTakeCareCustomer $report */
                $this->recordIsActive(ReportUserTakeCareCustomer::class, $user->id, $isActive);
                $this->recordIsActive(ReportUserQuotationCustomer::class, $user->id, $isActiveQuotation);
                $this->recordIsActive(ReportUserOrderedCustomer::class, $user->id, $isActiveOrder);
                $this->recordIsActive(ReportUserCounselingCustomer::class, $user->id, $isActiveCounselor);
                $this->recordIsActive(ReportUserComplainCustomer::class, $user->id, $isActiveComplain);
                $user->syncPermissions($permissions->pluck('name')->toArray());
            }
        );
    }

    private function recordIsActive(string $model, string $userId, bool $isActive): void
    {
        $report = (new $model)::query()->firstOrCreate(['user_id' => $userId]);
        $report->status = $isActive;
        $report->save();
    }

    public function getStaffHasPermission(string $permission)
    {
        return Staff::query()->permission($permission)->select('id', 'name')->get();
    }
}
