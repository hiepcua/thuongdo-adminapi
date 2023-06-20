<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
use App\Http\Requests\CustomerChangeStaffToMultipleRequest;
use App\Http\Requests\CustomerChangeStatusRequest;
use App\Http\Requests\CustomerUpdateSomethingRequest;
use App\Http\Resources\Customer\CustomerResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Customer;
use App\Models\CustomerOffer;
use App\Services\CustomerService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

/**
 * Class CustomerController
 * @package App\Http\Controllers
 */
class CustomerController extends Controller implements StoreValidationInterface, UpdateValidationInterface
{
    public function __construct(CustomerService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'name' => 'required|max:255',
            'phone_number' => 'required|max:13|unique:customers',
            'email' => 'required|max:255|email|unique:customers',
            'password' => 'required|max:255',
            'bod' => 'nullable|date_format:Y-m-d',
            'warehouse_id' => 'required|max:255|exists:warehouses,id',
            'address' => 'nullable|string|max:255',
            'staff_care_id' => 'nullable|exists:users,id',
            'staff_counselor_id' => 'nullable|exists:users,id',
            'service' => 'required|in:0,1',
            'facebook_url' => 'url|max:255',
            'skype_url' => 'url|max:255',
        ];
    }

    public function updateMessage(): array
    {
        return [];
    }

    /**
     * @param  string  $id
     * @return array
     */
    public function updateRequest(string $id): array
    {
        $data = $this->storeMessage();
        ValidationHelper::prepareUpdateAction($data, $id);
        return $data;
    }

    /**
     * @return JsonResponse
     */
    public function reports(): JsonResponse
    {
        return resSuccessWithinData((new ReportService())->reports(Customer::class));
    }

    /**
     * @param  CustomerUpdateSomethingRequest  $request
     * @param  Customer  $customer
     * @return JsonResponse
     */
    public function updateSomething(CustomerUpdateSomethingRequest $request, Customer $customer): JsonResponse
    {
        return $this->prepareUpdate($customer);
    }

    /**
     * @param  CustomerChangeStatusRequest  $request
     * @param  Customer  $customer
     * @return JsonResponse
     */
    public function changeStatus(CustomerChangeStatusRequest $request, Customer $customer): JsonResponse
    {
        return $this->prepareUpdate($customer);
    }

    /**
     * @param  Customer  $customer
     * @return JsonResponse
     */
    private function prepareUpdate(Customer $customer): JsonResponse
    {
        $customer->update(request()->all());
        return resSuccessWithinData($customer);
    }

    /**
     * @param  CustomerChangeStaffToMultipleRequest  $request
     * @return JsonResponse
     */
    public function changeStaff(CustomerChangeStaffToMultipleRequest $request): JsonResponse
    {
        Customer::query()->whereIn('id', $request->input('customers'))->update(
            request()->only(['staff_care_id', 'staff_order_id', 'staff_counselor_id'])
        );
        return resSuccess();
    }

    /**
     * detail: show detail of customer
     * @param  string  $id
     * @return JsonResponse
     */
    public function detail(string $id): JsonResponse
    {
        $customer = Customer::query()->findOrFail($id);
        return resSuccessWithinData(new CustomerResource($customer));
    }

    public function updateOffer(string $id): JsonResponse
    {
        $offer = CustomerOffer::query()->findOrFail($id)->update(request()->all());
        return resSuccessWithinData($offer);
    }
}
