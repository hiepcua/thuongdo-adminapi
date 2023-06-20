<?php


namespace App\Services;


use App\Constants\LocateConstant;
use App\Helpers\PaginateHelper;
use App\Http\Resources\TransporterResource;
use Illuminate\Http\JsonResponse;

class TransporterService extends BaseService
{
    protected string $_resource = TransporterResource::class;

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return resSuccessWithinData(
            new $this->_listResource(
                $this->_model->newQuery()->where('country', LocateConstant::COUNTRY_VI)->limit(
                    PaginateHelper::getLimit()
                )->orderBy('order')->get(), $this->_resource
            )
        );
    }
}