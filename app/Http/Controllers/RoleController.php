<?php

namespace App\Http\Controllers;

use App\Services\RoleService;

/**
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller 
{
      /**
     * DashboardController constructor.
     * @param  RoleService $service
     */
    public function __construct(RoleService $service)
    {
        $this->_service = $service;
    }
}