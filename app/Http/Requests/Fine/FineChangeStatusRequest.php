<?php

namespace App\Http\Requests\Fine;

use App\Constants\FineConstant;
use Illuminate\Foundation\Http\FormRequest;

class FineChangeStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'required|in:'.implode(',', array_keys(FineConstant::STATUSES))
        ];
    }

    public function attributes()
    {
        return ['status' => 'Trạng thái'];
    }
}
