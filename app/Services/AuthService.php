<?php


namespace App\Services;


use App\Constants\AuthConstant;
use App\Constants\OrganizationConstant;
use App\Constants\TimeConstant;
use App\Exceptions\ResponseException;
use App\Exceptions\ResponseWithinDataException;
use App\Jobs\Auth\UnlockAccountJob;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    /**
     * @description: Đăng nhập sử dụng email và password
     * @param  string  $email
     * @param  string  $password
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signIn(string $email, string $password): JsonResponse
    {
        /** @var User $user */
        $user = (new UserService())->getUserByEmail($email);
        // Kiểm tra tài khoản bị khóa
        throw_if(
            !$user->status,
            ResponseWithinDataException::class,
            [
                'countdown' => $this->getCountDownUnlockAccount($user->blocked_at), // thời gian còn lại
                'blocked_at' => optional($user->blocked_at)->format(TimeConstant::DATETIME_VI),
            ],
            __('auth.blocked')
        );
        if (Hash::check($password, $user->password)) {
            Auth::login($user);
            $user->organization_id = $user->organization_id ?? OrganizationConstant::ADMIN_ORGANIZATION;
            return resSuccessWithinData(
                [
                    'token' => $user->createToken($email)->plainTextToken,
                    'user' => $user->only(['id', 'name', 'email', 'organization_id'])
                ]
            );
        }
        // Sai mật khẩu
        $this->reportLoginFailed($user);
        throw new ResponseException(trans('auth.password'));
    }

    /**
     * @param  ?Carbon  $blockedAt
     * @return float|int
     */
    private function getCountDownUnlockAccount(?Carbon $blockedAt): int
    {
        if (!$blockedAt) {
            return 0;
        }
        $times = AuthConstant::ACCOUNT_BLOCKED_BY_MINUTES;
        $times = $times * 60 - now()->addMinutes($times)->diffInSeconds($blockedAt);
        return $times > 0 ? $times : 0;
    }

    /**
     * Thống kê số lần login sai mật khẩu
     * @param  User  $user
     */
    private function reportLoginFailed(User $user): void
    {
        $user->login_failed++;
        if ($user->login_failed === AuthConstant::LOGIN_MAXIMUM_INCORRECT_PASSWORD) {
            $user->blocked_at = now()->addMinutes(AuthConstant::ACCOUNT_BLOCKED_BY_MINUTES);
            $user->login_failed = 0;
            $user->status = AuthConstant::STATUS_BLOCK;
            // JOB sau 10' nữa tự động mở khóa tài khoản
            UnlockAccountJob::dispatch($user);
        }
        $user->save();
    }

    /**
     * Đăng ký sau đó đăng nhập
     *
     * @param  array  $data
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signUp(array $data): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->create(array_merge($data, ['password' => Hash::make($data['password'])]));
        Customer::query()->create(
            array_merge(
                $data,
                [
                    'code' => 'KH_'.Str::random(12),
                    'user_id' => $user->id
                ]
            )
        );


        return $this->signIn($data['email'], $data['password']);
    }
}