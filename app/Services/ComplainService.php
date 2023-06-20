<?php


namespace App\Services;


use App\Constants\ComplainConstant;
use App\Http\Resources\ComplainResource;
use App\Models\Complain;
use App\Models\ComplainFeedback;

class ComplainService extends BaseService
{
    protected string $_resource = ComplainResource::class;

    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return Complain::query()->where(
            ['order_id' => $orderId, 'status' => ComplainConstant::KEY_STATUS_DONE]
        )->count();
    }

    public function getCommentsById(string $id)
    {
        return ComplainFeedback::query()->where('complain_id', $id)->get();
    }
}