<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Helpers\ValidationHelper;
use App\Services\ReportDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     * @param  ReportDashboardService  $service
     */
    public function __construct(ReportDashboardService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function index(): JsonResponse
    {
        throw_if(
            $errors = ValidationHelper::validation(
                request()->all(),
                ['start_date' => 'required|date:Y-m-d', 'end_date' => 'required|date:Y-m-d|after_or_equal:start_date']
            ),
            CustomValidationException::class,
            $errors
        );
        return $this->_service->index(Carbon::parse(request('start_date')), Carbon::parse(request('end_date')));
    }
}
