<?php

namespace App\Http\Controllers;

use App\Services\LabelService;

class LabelController extends Controller
{
    public function __construct(LabelService $service)
    {
        $this->_service = $service;
    }
}
