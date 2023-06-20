<?php

namespace App\Http\Controllers;

use App\Exceptions\ResponseException;
use App\Exceptions\ResponseWithinDataException;
use App\Helpers\PaginateHelper;
use App\Helpers\ValidationHelper;
use App\Services\Service;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public Service $_service;

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function index(): JsonResponse
    {
        // Validation
        $this->throwValidationAndAction(__FUNCTION__);
        return $this->_service->index(PaginateHelper::getLimit());
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function pagination(): JsonResponse
    {
        $this->throwAction(__FUNCTION__);
        return $this->_service->pagination(PaginateHelper::getPerPage());
    }

    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__);
        return resSuccessWithinData($this->_service->store(request()->all()));
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function update(string $id): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__, $id);
        return resSuccessWithinData($this->_service->update($id, request()->all()));
    }

    /**
     * Xóa bản ghi
     * @param  string  $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__, $id);
        return $this->_service->destroy($id);
    }

    /**
     * Chi tiết bản ghi
     * @param  string  $id
     * @return JsonResponse
     */
    public function detail(string $id): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__, $id);
        return $this->_service->detail($id);
    }

    /**
     * @param  string  $function
     * @throws \Throwable
     */
    protected function throwValidationAndAction(string $function, ?string $id = null)
    {
        $this->throwValidation($function, $id);
        $this->throwAction($function);
    }

    /**
     * @param  string  $action
     * @throws \Throwable
     */
    public function throwValidation(string $action, ?string $id = null)
    {
        $actionRequest = "${action}Request";
        throw_if(
            method_exists($this, $actionRequest) && $errors = ValidationHelper::validation(
                request()->all(),
                $id ? $this->{$actionRequest}($id) : $this->{$actionRequest}(),
                $this->getAttributes(),
                $this->{"${action}Message"}()
            ),
            ResponseWithinDataException::class,
            $errors ?? [],
            trans('system.bad_request'),
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Kiểm tra action
     * @param  string  $action
     * @throws \Throwable
     */
    private function throwAction(string $action)
    {
        throw_unless(
            method_exists($this->_service, $action),
            ResponseException::class,
            trans('system.method_not_exists', ['method' => $action])
        );
    }

    /**
     * Custom Attribute Name
     * @return array
     */
    protected function getAttributes(): array
    {
        return [];
    }
}
