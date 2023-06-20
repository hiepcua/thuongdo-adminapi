<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NoteOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required|max:500',
            'supplier_id' => 'exists:suppliers,id',
            'is_public' => 'required|bool'
        ];
    }

    public function attributes()
    {
        return [
            'content' => 'Nội dung',
            'supplier_id' => 'Nhà cung cấp',
            'is_public' => 'Loại'
        ];
    }
}
