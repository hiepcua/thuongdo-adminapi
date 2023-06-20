<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListResource;
use App\Http\Resources\Warehouse\WarehouseGroupByCountryResource;
use App\Http\Resources\Warehouse\WarehouseListResource;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * WarehouseController constructor.
     * @param  WarehouseService  $warehouseService
     */
    public function __construct(WarehouseService $warehouseService)
    {
        $this->_service = $warehouseService;
    }

    /**
     * @param  string  $country
     * @return JsonResponse
     */
    public function getListByCountry(string $country): JsonResponse
    {
        return resSuccessWithinData(
            new WarehouseListResource(
                Warehouse::query()->where('country', $country)->get()
            )
        );
    }

    /**
     * @param  string  $country
     * @return JsonResponse
     */
    public function getListGroupByProvince(string $country): JsonResponse
    {
        return resSuccessWithinData(
            new WarehouseGroupByCountryResource(
                Warehouse::query()->where('country', $country)->get()->groupBy('province_id')
            )
        );
    }
}
