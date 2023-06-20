<?php

namespace App\Http\Controllers;

use App\Constants\CustomerConstant;
use App\Http\Resources\OnlyValueKeyResource;
use App\Services\CommonService;
use Illuminate\Http\JsonResponse;

class CommonController extends Controller
{
    public function __construct(CommonService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getCategoriesCustomer(): JsonResponse
    {
        return resSuccessWithinData($this->_service->getCategoriesCustomer());
    }

    /**
     * @return JsonResponse
     */
    public function getListCategoriesOrder(): JsonResponse
    {
        return resSuccessWithinData($this->_service->getCategoriesOrder());
    }

    public function getListCategoriesFine(): JsonResponse
    {
        return resSuccessWithinData($this->_service->getListCategoriesFine());
    }

    public function getCategoriesWithdrawal(): JsonResponse
    {
        return resSuccessWithinData(['statuses' => new OnlyValueKeyResource(CustomerConstant::WITHDRAWAL_STATUSES)]);
    }

    public function getCategoriesPackages(): JsonResponse
    {
        return resSuccessWithinData($this->_service->getCategoriesPackages());
    }

    public function getCategoriesDelivery(): JsonResponse
    {
        $customerId = request()->query('customer_id');
        $deliveryId = request()->query('delivery_id');
        return resSuccessWithinData($this->_service->getCategoriesDelivery($customerId, $deliveryId));
    }

    public function getCategoriesComplain(): JsonResponse
    {
        return resSuccessWithinData($this->_service->getCategoriesComplain());
    }
}
