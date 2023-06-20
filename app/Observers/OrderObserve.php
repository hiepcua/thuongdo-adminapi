<?php

namespace App\Observers;

use App\Constants\CustomerConstant;
use App\Constants\OrderConstant;
use App\Models\Order;
use App\Services\ActivityService;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\ReportCustomerService;
use App\Services\ReportOrganizationService;
use App\Services\Service;

class OrderObserve
{
    private Service $_activityService;
    private CustomerService $_customerService;
    private ReportCustomerService $_reportCustomerService;
    private ReportOrganizationService $_reportOrganizationService;
    private OrderService $_orderService;

    public function __construct(
        ActivityService $active,
        CustomerService $customer,
        ReportCustomerService $reportCustomer,
        ReportOrganizationService $organizationService,
        OrderService $orderService
    ) {
        $this->_activityService = $active;
        $this->_customerService = $customer;
        $this->_reportCustomerService = $reportCustomer;
        $this->_reportOrganizationService = $organizationService;
        $this->_orderService = $orderService;
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        $status = $order->status;

        if ($order->getOriginal('status') !== $status) {
            $oldStatus = $order->getOriginal('status') ?? OrderConstant::KEY_STATUS_WAITING_QUOTE;
            $this->_reportCustomerService->changeStatus(
                $oldStatus,
                $status,
                $order->customer_id
            );
            $this->_reportOrganizationService->orderChangeStatus(
                $oldStatus,
                $status,
                $order->customer->organization_id
            );
            $this->_activityService->setOrderLog(
                $order,
                trans(
                    "activity.order_$status",
                    ['deposit' => number_format($order->deposit_cost).'đ']
                ),
                $order->id
            );
            $this->_orderService->setStatusTime($order->id, $order->status);
        }

        (new ActivityService())->activitiesOrder($order);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        $this->_reportCustomerService->decrementByKey($order->status, $order->customer_id);
        $this->_reportCustomerService->decrementByKey(CustomerConstant::KEY_REPORT_ORDER, $order->customer_id);
        $this->_reportOrganizationService->decrementByKey($order->status, $order->customer_id);
        $this->_orderService->deleteByOrderId($order->id);
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
