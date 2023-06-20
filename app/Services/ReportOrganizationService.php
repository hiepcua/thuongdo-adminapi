<?php


namespace App\Services;


use App\Models\ReportOrganizationOrder;
use Illuminate\Support\Facades\Auth;

class ReportOrganizationService implements Service
{
    private OrderService $_orderService;

    public function __construct()
    {
        $this->_orderService = new OrderService();
    }

    /**
     * @param  string  $key
     * @param  string  $organizationId
     * @param  int|null  $amount
     */
    public function incrementByKey(string $key, string $organizationId, ?int $amount = 1): void
    {
        $this->getReportOrganizationCurrent($organizationId)->increment($key, $amount);
    }

    /**
     * @param  string  $key
     * @param  string  $organizationId
     * @param  float|null  $amount
     */
    public function decrementByKey(string $key, string $organizationId, ?float $amount = 1): void
    {
        $report = $this->getReportOrganizationCurrent($organizationId);
        if ($report->{$key} <= $amount) {
            $report->{$key} = 0;
            $report->save();
        } else {
            $this->getReportOrganizationCurrent($organizationId)->decrement($key, $amount);
        }
    }

    public function orderChangeStatus(string $statusOld, string $statusNew, string $organizationId)
    {
        $this->incrementByKey($statusNew, $organizationId);
        $this->decrementByKey($statusOld, $organizationId);
    }

    public function getReportOrganizationCurrent(string $organizationId)
    {
        return ReportOrganizationOrder::query()->firstOrCreate(['organization_id' => $organizationId]);
    }
}