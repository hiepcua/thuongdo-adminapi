<?php

namespace App\Http\Requests\Carrier;

use App\Constants\CarrierConstant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarrierPriceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'carrier' => 'required|in:'.implode(',', CarrierConstant::CARRIERS),
            'delivery_type' => [
                'in:normal,fast',
                Rule::requiredIf(
                    array_search(
                        request()->input('carrier'),
                        CarrierConstant::CARRIERS_HAS_DELIVERY_TYPE
                    ) !== false
                )
            ],
            'packages' => 'required|array',
            'packages.*' => 'required|exists:order_package,id',
            'customer_delivery_id' => 'required|exists:customer_deliveries,id',
            'customer_id' => 'required|exists:customers,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'carrier' => 'Đơn vị vận chuyển',
            'delivery_type' => 'Loại hình',
            'packages' => 'Kiện',
            'packages.*' => 'Kiện'
        ];
    }
}
