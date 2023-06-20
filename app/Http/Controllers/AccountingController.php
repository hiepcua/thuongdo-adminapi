<?php

namespace App\Http\Controllers;

use App\Http\Requests\Accounting\AccountingRequest;
use App\Http\Requests\Accounting\InspectionRequest;
use App\Http\Requests\Accounting\InternationalCostRequest;
use App\Models\Customer;
use App\Services\AccountingService;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->_service = new AccountingService();
    }

    /**
     * @param  AccountingRequest  $request
     * @return JsonResponse
     */
    public function getOrderFee(AccountingRequest $request): JsonResponse
    {
        $level = (new CustomerService())->getCustomerLevelById($request->customer_id);
        return resSuccessWithinData(
            [
                'order_fee' => $this->_service->getOrderFee($request->amount),
                'discount_cost' => $this->_service->getCustomerLevelCost($request->amount, $level)
            ]
        );
    }

    /**
     * @param  InspectionRequest  $request
     * @return JsonResponse
     */
    public function getInspectionCost(InspectionRequest $request): JsonResponse
    {
        return resSuccessWithinData($this->_service->getInspectionCost($request->quantity));
    }

    public function getInternationalCost(InternationalCostRequest $request): JsonResponse
    {
       $params = $request->all();
       $weight = $params['weight'] ?? 0;
       $volume = isset($params['height']) ? ((float)$params['height'] * (float)$params['width'] * (float)$params['length']) : 0;
       return resSuccessWithinData($this->_service->getInternationShippingCost($params['province_id'], $weight, $volume));

    }
}
