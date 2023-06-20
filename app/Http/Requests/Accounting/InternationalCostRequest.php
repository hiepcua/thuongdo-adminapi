<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class InternationalCostRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'province_id' => 'required|exists:provinces,id',
            'weight' => 'required_without_all:height,width,length|numeric|min:0',
            'width' => 'required_without:weight|numeric|min:0',
            'height' => 'required_without:weight|numeric|min:0',
            'length' => 'required_without:weight|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id'
        ];
    }

    public function attributes()
    {
        return [
            'province_id' => 'Tỉnh thành',
            'weight' => 'Cân nặng',
            'width' => 'Chiều rộng',
            'height' => 'Chiều cao',
            'length' => 'Chiều dài',
            'customer_id' => 'Khách hàng'
        ];
    }
}
