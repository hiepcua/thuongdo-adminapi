<?php

namespace App\Http\Requests\Order;

use App\Constants\OrderConstant;
use Illuminate\Foundation\Http\FormRequest;

class OrderChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' =>'required|in:'. implode(',', OrderConstant::getStatusKeys())
        ];
    }
}
