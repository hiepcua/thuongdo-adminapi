<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCancelRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason_cancel' => 'required|max:500'
        ];
    }

    public function getAttributes()
    {
        return ['reason_cancel' => 'Lý do'];
    }
}
