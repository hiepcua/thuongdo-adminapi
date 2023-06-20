<?php

namespace App\Http\Requests\Delivery;

use App\Constants\DeliveryConstant;
use App\Constants\PhoneNumberConstant;
use App\Helpers\ConvertHelper;
use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;

class ModifiesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_received' => 'nullable|bool',
            'status' => 'nullable|in:'.implode(',', array_keys(DeliveryConstant::STATUSES)),
            'extend_cost' => 'nullable|min:0|numeric',
            'shipper_phone_number' => 'nullable|max:10|starts_with:'.ConvertHelper::arrayToString(
                    PhoneNumberConstant::PREFIX
                ),
            'note' => 'nullable|max:500',
            'postcode' => 'nullable|max:20',
            'date' => 'nullable|date:Y-m-d',
            'customer_delivery_id' => 'nullable|exists:customer_deliveries,id'
        ];
    }

    public function attributes()
    {
        return [
            "is_received" => "Trạng thái nhận hàng",
            "status" => "Tráng thái",
            "extend_cost" => "Phí ship",
            "is_paid_extend" => "Thanh toán phí ship",
            "shipper_phone_number" => "Số điện thoại Shipper",
            "note" => "Ghi chú",
            'postcode' => 'Mã bưu điện',
            'date' => 'Ngày giao',
            'customer_delivery_id' => 'Người nhận'
        ];
    }
}
