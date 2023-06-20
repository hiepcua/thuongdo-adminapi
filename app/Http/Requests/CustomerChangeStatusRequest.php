<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:0,1',
            'customer_reason_inactive_id' => 'required_if:status,0|exists:customer_reason_inactive,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_reason_inactive_id' => 'Lý do đóng'
        ];
    }
}
