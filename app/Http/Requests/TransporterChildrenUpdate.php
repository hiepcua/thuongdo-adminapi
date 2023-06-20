<?php

namespace App\Http\Requests;

use App\Constants\PhoneNumberConstant;
use App\Helpers\ConvertHelper;
use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class TransporterChildrenUpdate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'phone_number' => 'max:10|starts_with:'.ConvertHelper::arrayToString(
                    PhoneNumberConstant::PREFIX
                ),
        ];
    }
}
