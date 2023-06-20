<?php

namespace App\Observers;

use App\Constants\ActivityConstant;
use App\Constants\CustomerConstant;
use App\Constants\PackageConstant;
use App\Models\Delivery;
use App\Models\OrderPackage;
use App\Models\OrderPackageStatusTime;
use App\Models\ReportPackage;
use App\Services\ActivityService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\ReportCustomerService;

class OrderPackageObserve
{
    private ReportCustomerService $_reportCustomerService;

    public function __construct(ReportCustomerService $reportCustomerService)
    {
        $this->_reportCustomerService = $reportCustomerService;
    }

    /**
     * Handle the OrderPackage "created" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function created(OrderPackage $orderPackage)
    {
        if($orderPackage->order_id) (new OrderService())->incrementByColumn($orderPackage->order_id, 'packages_number');
        (new OrderPackageService())->reportStatusTime($orderPackage->id, PackageConstant::STATUS_PENDING);
        ReportPackage::query()->firstOrCreate(['customer_id' => $orderPackage->customer_id, 'organization_id' => getOrganization()])->increment(
            PackageConstant::STATUS_PENDING
        );
        $this->_reportCustomerService->incrementByKey(CustomerConstant::KEY_REPORT_PACKAGE, $orderPackage->customer_id);
    }

    /**
     * Handle the OrderPackage "updated" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function updated(OrderPackage $orderPackage)
    {
        (new OrderPackageService())->storeActivity($orderPackage);
        if (($old = $orderPackage->getOriginal('delivery_id')) != $orderPackage->delivery_id) {
            $delivery = $orderPackage->delivery ?? Delivery::query()->find($old);
            (new ActivityService())->setLog(
                $delivery,
                $orderPackage->delivery_id ? 'Thêm mới' : 'Hủy',
                ActivityConstant::DELIVERY_PACKAGE,
                null,
                json_encode(['package' => $orderPackage->bill_code])
            );
        }
    }

    /**
     * Handle the OrderPackage "deleted" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function deleted(OrderPackage $orderPackage)
    {
        $this->_reportCustomerService->decrementByKey(CustomerConstant::KEY_REPORT_PACKAGE, $orderPackage->customer_id);
    }

    /**
     * Handle the OrderPackage "restored" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function restored(OrderPackage $orderPackage)
    {
        //
    }

    /**
     * Handle the OrderPackage "force deleted" event.
     *
     * @param  \App\Models\OrderPackage  $orderPackage
     * @return void
     */
    public function forceDeleted(OrderPackage $orderPackage)
    {
        //
    }
}
