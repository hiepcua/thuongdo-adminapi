<?php

namespace App\Http\Requests\Accounting;

use Illuminate\Foundation\Http\FormRequest;

class AccountingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'customer_id' => 'required|exists:customers,id'
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'Số tiền',
            'customer_id' => 'Khách hàng'
        ];
    }
}
