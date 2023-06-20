<?php

namespace App\Http\Controllers;

use App\Services\DepartmentService;

/**
 * Class DepartmentController
 * @package App\Http\Controllers
 */
class DepartmentController extends Controller 
{
      /**
     * DashboardController constructor.
     * @param  DepartmentService $service
     */
    public function __construct(DepartmentService $service)
    {
        $this->_service = $service;
    }
}