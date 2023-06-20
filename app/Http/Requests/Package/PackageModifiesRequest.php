<?php

namespace App\Http\Requests\Package;

use App\Constants\PackageConstant;
use Illuminate\Foundation\Http\FormRequest;

class PackageModifiesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bill_code' => 'unique:order_package,bill_code,' . $this->package->id,
            'transporter' => 'max:255',
            'category_id' => 'nullable|exists:categories,id',
            'weight' => 'nullable|min:0',
            'height' => 'nullable|min:0',
            'width' => 'nullable|min:0',
            'length' => 'nullable|min:0',
            'status' => 'in:' . implode(',', array_keys(PackageConstant::STATUSES)),
            'china_shipping_cost_cny' => 'nullable|min:0',
            'note' => 'max:500',
            'note_ordered' => 'max:500',
            'code_po' => 'unique:order_package,code_po',
            'package_number' => 'nullable|min:0'
        ];
    }

    public function attributes()
    {
        return [
            'transporter' => 'Hãng vận chuyển',
            'china_shipping_cost_cny' => 'Phí vận chuyển nội địa TQ',
            'note' => 'Ghi chú',
            'note_ordered' => 'Ghi chú đặt hàng',
            'code_po' => 'Mã đặt hàng',
            'package_number' => 'Số kiện'
        ];
    }
}
