<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class PackageStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:order_details,id',
            'products.*.quantity' => 'required|numeric|min:0',
            'code_po' => 'required|unique:order_package|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'transporter_id' => 'nullable|exists:transporters,id',
            'category_id' => 'nullable|required_without:category|exists:categories,id',
            'category' => 'nullable|required_without:category_id|max:255',
            'package_number' => 'numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'products' => 'Sản phẩm',
            'products.*.id' => 'Sản phẩm',
            'products.*.quantity' => 'Số lượng',
            'code_po' => 'Mã đặt hàng',
            'supplier_id' => 'Nhà cung cấp',
            'transporter_id' => 'Hãng vận chuyển',
            'category_id' => 'Danh mục',
            'category' => 'Danh mục',
            'package_number' => 'Số kiện',
        ];
    }
}
