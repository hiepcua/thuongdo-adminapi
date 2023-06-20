<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;

/**
 * Class PermissionController
 * @package App\Http\Controllers
 */
class PermissionController extends Controller 
{
      /**
     * DashboardController constructor.
     * @param  PermissionService $service
     */
    public function __construct(PermissionService $service)
    {
        $this->_service = $service;
    }

    public function getModulePermission()
    {
        return  $this->_service->getModulePermission();
    }
}