<?php

namespace App\Http\Controllers;


use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\CustomerDelivery;
use App\Services\CustomerDeliveryService;
use Illuminate\Http\JsonResponse;

/**
 * Class CustomerDeliveryController
 * @package App\Http\Controllers
 */
class CustomerDeliveryController extends Controller implements UpdateValidationInterface, StoreValidationInterface
{
    /**
     * DashboardController constructor.
     * @param  CustomerDeliveryService  $service
     */
    public function __construct(CustomerDeliveryService $service)
    {
        $this->_service = $service;
    }

    /**
     * @return array
     */
    public function updateMessage(): array
    {
        return [];
    }

    /**
     * @param string $id
     * @return string[]
     */
    public function updateRequest(string $id): array
    {
        return [
            'customer_id' => 'required',
            'receiver' => 'required|max:255',
            'address' => 'required|max:255',
            'phone_number' => 'required|max:10',
            'ward_id' => 'required|exists:wards,id',
            'district_id' => 'required|exists:districts,id',
            'province_id' => 'required|exists:provinces,id',
        ];
    }

    public function changeStatus(CustomerDelivery $customerDelivery): JsonResponse
    {
        CustomerDelivery::query()
            ->where('customer_id', request()->input('customer_id'))
            ->update(['is_default' => false]);
        $customerDelivery->update(['is_default' => true]);
        return resSuccessWithinData($customerDelivery);
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'receiver' => 'required|max:255',
            'address' => 'required|max:255',
            'phone_number' => 'required|max:10',
            'ward_id' => 'required|exists:wards,id',
            'district_id' => 'required|exists:districts,id',
            'province_id' => 'required|exists:provinces,id',
        ];
    }
}
