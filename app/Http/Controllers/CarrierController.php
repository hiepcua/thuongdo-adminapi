<?php

namespace App\Http\Controllers;

use App\Http\Requests\Carrier\CarrierPriceRequest;
use App\Models\Customer;
use App\Models\CustomerDelivery;
use App\Services\Carriers\CarrierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CarrierController extends Controller
{
    public function __construct(CarrierService $service)
    {
        $this->_service = $service;
    }

    public function getPrice(CarrierPriceRequest $request): JsonResponse
    {
        $customer = Customer::query()->find($request->input('customer_id'));
        if (!$customer->warehouse_id) {
            return resError(trans('carrier.dont_set_warehouse'));
        }
        $price = $this->_service->getPrice(request()->all() + ['warehouse_id' => $customer->warehouse_id]);
        // Lưu lại phí tạm tính khi tạo xong giao hàng thì trừ (không phải gọi lên bên vận chuyển lần thứ 2)
        CustomerDelivery::query()->findOrFail(request()->input('customer_delivery_id'))->update(
            ['delivery_cost' => $price > 0 ? $price : 0]
        );
        if ($price == -1) {
            return resError(trans('carrier.throw'));
        }
        return resSuccessWithinData($price);
    }
}
