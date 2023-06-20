<?php

namespace App\Http\Requests;

use App\Constants\CustomerConstant;
use App\Constants\ViaConstant;
use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateSomethingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "label_id" => "required_without_all:via,business_type,staff_care_id,staff_order_id,staff_counselor_id|exists:labels,id",
            "via" => "required_without_all:label_id,business_type,staff_care_id,staff_order_id,staff_counselor_id|in:".implode(
                    ',',
                    array_keys(ViaConstant::STATUSES)
                ),
            "business_type" => "required_without_all:label_id,via,staff_care_id,staff_order_id,staff_counselor_id|in:".implode(
                    ',',
                    array_keys(CustomerConstant::CUSTOMER_BUSINESS_TYPE)
                ),
            "staff_care_id" => "required_without_all:label_id,business_type,via,staff_order_id,staff_counselor_id|uuid|exists:users,id",
            "staff_order_id" => "required_without_all:label_id,business_type,via,staff_care_id,staff_counselor_id|uuid|exists:users,id",
            "staff_counselor_id" => "required_without_all:label_id,business_type,via,staff_order_id,staff_care_id|uuid|exists:users,id"
        ];
    }


    public function attributes(): array
    {
        return [
            "label_id" => "Nhãn",
            "via" => "Nguồn tương tác",
            "business_type" => "Loại Hình",
            "staff_care_id" => "Nhân viên chăm sóc",
            "staff_order_id" => "Nhân viên đặt hàng",
            "staff_counselor_id" => "Nhân viên tư vấn"
        ];
    }
}
