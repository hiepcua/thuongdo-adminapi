<?php

namespace App\Http\Controllers;

use App\Services\ReportCeoService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(ReportService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getCEO(): JsonResponse
    {
        return resSuccessWithinData((new ReportCeoService())->getReportCEO());
    }

    public function getWarehouse(string $id): JsonResponse
    {
        return resSuccessWithinData((new ReportCeoService())->getChartWarehouses($id));
    }

    public function getCategories(string $type): JsonResponse
    {
        return resSuccessWithinData((new ReportCeoService())->getChartCategories($type));
    }
}
