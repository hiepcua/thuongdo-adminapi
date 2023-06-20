<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class StaffChangePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required|max:255',
            'new_password' => 'required|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'password' => 'Mật khẩu cũ',
            'new_password' => 'Mật khẩu mới'
        ];
    }
}
