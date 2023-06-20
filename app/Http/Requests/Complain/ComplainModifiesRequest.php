<?php

namespace App\Http\Requests\Complain;

use App\Constants\ComplainConstant;
use Illuminate\Foundation\Http\FormRequest;

class ComplainModifiesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'nullable|in:'.implode(',', array_keys(ComplainConstant::STATUSES)),
            'solution_id' => 'nullable|exists:solutions,id'
        ];
    }
}
