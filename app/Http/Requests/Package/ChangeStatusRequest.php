<?php

namespace App\Http\Requests\Package;

use App\Constants\PackageConstant;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'packages' => 'required|array',
            'packages.*' => 'required|exists:order_package,id',
            "status" => 'required|in:' . implode(',', array_keys(PackageConstant::STATUSES))
        ];
    }

    public function attributes()
    {
        return [
            'packages' => 'Kiện hàng',
            'packages.*' => 'Kiện hàng',
        ];
    }
}
