<?php

namespace App\Http\Controllers;

use App\Services\ConfigService;

class ConfigController extends Controller
{
    public function __construct(ConfigService $service)
    {
        $this->_service = $service;
    }
}
