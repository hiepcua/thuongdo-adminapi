<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Requests\Auth\VerifyCodeEmailRequest;
use App\Mail\ForgotPassword;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class AuthController
 * @package App\Http\Controllers
 *
 * @property AuthService $_service
 * @property UserService $_userService
 */
class AuthController extends Controller
{
    private UserService $_userService;
    public function __construct(AuthService $service, UserService $userService)
    {
        $this->_service = $service;
        $this->_userService = $userService;
    }

    /**
     * Đăng nhập
     * @param  LoginRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signIn(LoginRequest $request): JsonResponse
    {
        return $this->_service->signIn($request->input('email'), $request->input('password'));
    }

    /**
     * Đăng xuất
     * @return JsonResponse
     */
    public function signOut(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return resSuccess(__('auth.logout'));
    }

    /**
     * Gửi code vào email người dùng để đặt lại mật khẩu
     * @param  string  $email
     * @return JsonResponse
     * @throws \Throwable
     */
    public function resetPasswordSendMail(string $email): JsonResponse
    {
        $this->_userService->getUserByEmail($email);
        Mail::to($email)->queue(new ForgotPassword($this->_userService->getVerifyCodeByEmail($email)));
        return resSuccess(__('auth.forgot_password_sent_email'));
    }

    /**
     * Confirm mã xác nhận
     * @param  VerifyCodeEmailRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function confirmVerifyCodeAndEmail(VerifyCodeEmailRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->where($request->only('email', 'verify_code'))->firstOrFail();
        //Reset lại mã xác minh
        $user->login_failed = 0;
        $user->save();
        return resSuccess();
    }

    /**
     * Thay đổi mật khẩu dựa trên email và mã xác minh.
     * @param  ChangePasswordRequest  $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        return $this->_userService->changePassword($request->input('email'), $request->input('password'));
    }

    /**
     * Đăng ký
     * @param  SignUpRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function signUp(SignUpRequest $request): JsonResponse
    {
        return $this->_service->signUp($request->all());
    }

}
