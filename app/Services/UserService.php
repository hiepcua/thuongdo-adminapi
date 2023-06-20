<?php


namespace App\Services;


use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService extends BaseService
{
    protected string $_resource = UserResource::class;
    /**
     * Lấy mã code để reset password
     * @param  string  $email
     * @return string|null
     */
    public function getVerifyCodeByEmail(string $email): ?string
    {
        /** @var User $user */
        $user = $this->getUserByEmail($email);
        if(!$user) return null;
        $user->verify_code = Str::upper(Str::random(5));
        $user->save();
        return $user->verify_code;
    }

    /**
     * Lấy thông tin user qua email
     * @param  string  $email
     * @return Builder|Model|object|null
     */
    public function getUserByEmail(string $email)
    {
        return User::query()->withoutGlobalScope(OrganizationScope::class)->where('email', $email)->select(
            'id',
            'name',
            'email',
            'password',
            'login_failed',
            'blocked_at',
            'organization_id',
            'status'
        )->firstOrFail();
    }

    /**
     * Thay đổi mật khẩu
     * @param  string  $email
     * @param  string  $password
     * @return JsonResponse
     */
    public function changePassword(string $email, string $password): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUserByEmail($email);
        $user->password = Hash::make($password);
        $user->verify_code = null;
        $user->save();
        return resSuccess();
    }

    public function updateWarehouseForCurrentUser()
    {}

    /**
     *  Lấy số lượng nhân viên theo tháng đã đăng ký
     * @return int
     */
    public function getUserNumberByYearAndMonth(): int
    {
        return User::query()->withoutGlobalScope(OrganizationScope::class)->whereMonth('created_at', date('m'))->whereYear(
            'created_at',
            date('Y')
        )->count();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function updatePassword($data) {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }
}
