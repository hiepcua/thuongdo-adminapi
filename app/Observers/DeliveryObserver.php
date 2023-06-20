<?php

namespace App\Observers;

use App\Constants\ActivityConstant;
use App\Constants\DeliveryConstant;
use App\Http\Resources\ReportStatusResource;
use App\Models\Delivery;
use App\Models\ReportDelivery;
use App\Services\ActivityService;
use App\Services\ReportService;

class DeliveryObserver
{
    public function created(Delivery $delivery)
    {
        (new ReportService())->incrementByOrganization(ReportDelivery::class, DeliveryConstant::KEY_STATUS_PENDING);
    }

    public function updated(Delivery $delivery)
    {
        if (($old = $delivery->getOriginal('status')) != $delivery->status) {
            (new ReportService())->inDecrementByOrganization(ReportDelivery::class, $delivery->status, $old);
            (new ActivityService())->setLog(
                $delivery,
                DeliveryConstant::STATUSES[$delivery->status],
                ActivityConstant::DELIVERY_STATUS,
                null,
                json_encode(['status' => new ReportStatusResource($delivery->status, DeliveryConstant::STATUSES, DeliveryConstant::STATUSES_COLOR)])
            );
        }
    }
}
