<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransporterChildrenStore;
use App\Http\Requests\TransporterChildrenUpdate;
use App\Models\TransporterDetail;
use App\Services\TransporterService;
use Illuminate\Http\JsonResponse;

class TransporterController extends Controller
{
    public function __construct(TransporterService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getChildren(string $id): JsonResponse
    {
        return resSuccessWithinData(
            TransporterDetail::query()->where('transporter_id', $id)->select('id', 'name', 'phone_number')->get()
        );
    }

    /**
     * @param  TransporterChildrenStore  $request
     * @param  string  $id
     * @return JsonResponse
     */
    public function storeChildren(TransporterChildrenStore $request, string $id): JsonResponse
    {
        $params = $request->all();
        $params['transporter_id'] = $id;
        $detail = TransporterDetail::query()->create($params);
        return resSuccessWithinData($detail);
    }

    /**
     * @param  TransporterChildrenUpdate  $request
     * @param  string  $detailId
     * @return JsonResponse
     */
    public function updateChildren(TransporterChildrenUpdate $request, string $detailId): JsonResponse
    {
        optional(TransporterDetail::query()->findOrFail($detailId))->update($request->all());
        return resSuccess();
    }

    /**
     * @param  string  $detailId
     * @return JsonResponse
     */
    public function destroyChildren(string $detailId): JsonResponse
    {
        optional(TransporterDetail::query()->findOrFail($detailId))->delete();
        return resSuccess();
    }
}
