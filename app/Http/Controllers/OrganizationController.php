<?php

namespace App\Http\Controllers;

use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Staff;
use App\Services\OrganizationService;
use Illuminate\Validation\Rule;

/**
 * Class OrganizationController
 * @package App\Http\Controllers
 */
class OrganizationController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
      /**
     * DashboardController constructor.
     * @param  OrganizationService $service
     */
    public function __construct(OrganizationService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): ?array
    {
       return [];
    }

    public function storeRequest(): array
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email|unique:organizations,email',
            'phone_number' => 'required|max:10|unique:organizations,phone_number',
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        return [
            'name' => 'required|max:255',
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('organizations')->ignore($id),
            ],
            'phone_number' => [
                'required',
                'max:10',
                Rule::unique('organizations')->ignore($id),
            ],
        ];
    }
}
