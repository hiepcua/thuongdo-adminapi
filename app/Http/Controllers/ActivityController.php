<?php

namespace App\Http\Controllers;

use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function __construct(ActivityService $service)
    {
        $this->_service = $service;
    }

    public function getOrderLog(string $orderId): JsonResponse
    {
        return $this->_service->getOrderLog($orderId);
    }

    public function getPackageLog(string $packageId): JsonResponse
    {
        return $this->_service->getPackageLog($packageId);
    }

    public function getPackageDetailLog(string $packageId): JsonResponse
    {
        return $this->_service->getPackageDetailLog($packageId);
    }

    public function getConsignmentLog(string $consignmentId): JsonResponse
    {
        return $this->_service->getConsignmentLog($consignmentId);
    }

    public function getDeliveryLog(string $deliveryId): JsonResponse
    {
        return $this->_service->getDeliveryLog($deliveryId);
    }
}
