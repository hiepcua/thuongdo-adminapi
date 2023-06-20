<?php


namespace App\Services;


use App\Constants\CustomerConstant;
use App\Constants\OrderConstant;
use App\Models\ReportCustomer;
use Illuminate\Support\Facades\Auth;

class ReportCustomerService implements Service
{
    private OrderService $_orderService;

    public function __construct()
    {
        $this->_orderService = new OrderService();
    }

    /**
     * @param  string  $key
     * @param  int|null  $amount
     * @param  string|null  $customerId
     */
    public function incrementByKey(string $key, string $customerId = null, ?int $amount = 1): void
    {
        $this->getReportCustomerCurrent($customerId)->increment($key, $amount);
    }

    /**
     * @param  string  $key
     * @param  string|null  $customerId
     * @param  int|null  $amount
     */
    public function decrementByKey(string $key, string $customerId = null, ?int $amount = 1): void
    {
        $report = $this->getReportCustomerCurrent($customerId);
        if ($report->{$key} <= $amount) {
            $report->{$key} = 0;
            $report->save();
        } else {
            $this->getReportCustomerCurrent($customerId)->decrement($key, $amount);
        }
    }

    public function changeStatus(string $statusOld, string $statusNew, string $customerId)
    {
        $this->incrementByKey($statusNew, $customerId);
        $this->decrementByKey($statusOld, $customerId);
    }

    public function getReportCustomerCurrent(?string $customerId = null)
    {
        $customerId =  $customerId ?? optional(Auth::user())->id;
        $query = ReportCustomer::query();
        if($customerId) return $query->firstOrCreate(['customer_id' => $customerId]);
        return $query->first();
    }

    public function updateLevel(string $customerId): void
    {
        $costs = optional($this->getReportCustomerCurrent($customerId)->first())->{CustomerConstant::KEY_REPORT_ORDER_COST} ?? 0;
        $level = (new ConfigService())->getLevelByCosts((float)$costs);

        (new ReportService())->reportLevel($level, (new CustomerService())->getCustomerById($customerId)->level);
        Auth::user()->update(['level' => $level]);
    }

    /**
     * @param  string  $customerId
     * @param  float  $amount
     */
    public function increaseOrderAmount(string $customerId, float $amount): void
    {
        $this->getReportCustomerCurrent($customerId)->increment('order_amount', $amount);
    }

    /**
     * @param  float  $amount
     * @param  string|null  $customerId
     */
    public function balanceAmountDecrease(float $amount, ?string $customerId = null): void
    {
        $this->decrementByKey(CustomerConstant::KEY_REPORT_BALANCE_AMOUNT, $customerId, $amount);
        $this->incrementByKey(CustomerConstant::KEY_REPORT_PURCHASE_AMOUNT, $customerId, $amount);
    }

    /**
     * @param  float  $amount
     * @param  string|null  $customerId
     */
    public function balanceAmountIncrease(float $amount, ?string $customerId = null): void
    {
        $this->incrementByKey(CustomerConstant::KEY_REPORT_BALANCE_AMOUNT, $customerId, $amount);
        $this->decrementByKey(CustomerConstant::KEY_REPORT_PURCHASE_AMOUNT, $customerId, $amount);
    }

    /**
     * Kiểm tra khách hàng mới
     *
     * @param ?string $customerId
     * @return boolean
     */
    public function isNewCustomerByCustomerId(?string $customerId): bool
    {
        if (!$customerId) {
            return false;
        }
        $report = optional(ReportCustomer::query()->where('customer_id', $customerId)->selectRaw(
                implode(',', OrderConstant::getStatusKeys())
            )->first())->toArray() ?? [];
        return array_sum(array_values($report)) === 1;
    }
}