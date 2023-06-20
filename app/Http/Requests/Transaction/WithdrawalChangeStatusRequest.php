<?php

namespace App\Http\Requests\Transaction;

use App\Constants\CustomerConstant;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:' . implode(',', array_keys(CustomerConstant::WITHDRAWAL_STATUSES))
        ];
    }
}
