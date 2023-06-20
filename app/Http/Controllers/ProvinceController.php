<?php

namespace App\Http\Controllers;

use App\Services\ProvinceService;

/**
 * Class ProvinceController
 * @package App\Http\Controllers
 */
class ProvinceController extends Controller
{
    /**
     * DashboardController constructor.
     * @param  ProvinceService $service
     */
    public function __construct(ProvinceService $service)
    {
        $this->_service = $service;
    }
}
