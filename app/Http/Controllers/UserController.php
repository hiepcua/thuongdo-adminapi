<?php

namespace App\Http\Controllers;

use App\Http\Requests\Staff\StaffChangePasswordRequest;
use App\Http\Resources\ListResource;
use App\Http\Resources\Role\RoleResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Staff;
use App\Services\MediaService;
use App\Services\PermissionService;
use App\Services\StaffService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(StaffService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return mixed
     */
    public function me()
    {
        /** @var Staff $staff */
        $staff = Staff::query()->where('id', Auth::user()->id)->first();
        $permissions = (new PermissionService())->getUserPermission($staff);
        $data = $staff->only(
            'id',
            'name',
            'email',
            'avatar',
            'phone_number'
        );
        $data['department'] = optional($staff->department)->only('id', 'name');
        $data['roles'] = (new ListResource($staff->roles, RoleResource::class));
        $data['permissions'] = $permissions;
        return resSuccessWithinData(
            $data
        );
    }

    public function storeMessage(): array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users,email',
            'phone_number' => 'required|max:10|unique:users,phone_number',
            'password' => 'required|max:255',
            'department_id' => 'required|max:255|exists:departments,id',
            'role_id' => 'required|max:255|exists:roles,id',
            'organization_id' => 'required|exists:organizations,id'
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        return [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users')->ignore($id),
            ],
            'phone_number' => [
                'required',
                'max:10',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'max:255',
            'organization_id' => 'required|exists:organizations,id',
            'role_id' => 'required|max:255|exists:roles,id'
        ];
    }

    /**
     * @param  Staff  $user
     * @return JsonResponse
     */
    public function activeUser(Staff $user): JsonResponse
    {
        $user->update(['status' => (int)!$user->status]);
        return resSuccessWithinData($user);
    }

    public function uploadAvt(): JsonResponse
    {
        $attachment = (new MediaService())->singleFile(request()->file('file'));
        Auth::user()->update(['avatar' => $attachment->id]);
        return resSuccessWithinData($attachment->only('id', 'url'));
    }

    /**
     * @param  StaffChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(StaffChangePasswordRequest $request): JsonResponse
    {
        $user = Auth::user();
        if (!Hash::check( $request->input('password'), $user->password)) {
            return resError(trans('staff.password_invalid'));
        }
        $user->password = Hash::make($request->input('new_password'));
        $user->save();
        return resSuccess();
    }
}
