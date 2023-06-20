<?php

namespace App\Http\Resources\Package;

use App\Constants\PackageConstant;
use App\Http\Resources\PaginateJsonResource;
use App\Http\Resources\Resource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ReportOrderVN;
use App\Models\ReportCustomer;
use App\Models\OrderPackage;
use App\Models\ReportPackage;
use App\Services\OrderPackageService;
use App\Services\ReportCustomerService;
use Illuminate\Support\Facades\Auth;
use App\Helpers\TimeHelper;
use App\Constants\TimeConstant;

class PackagePaginationResource extends PaginateJsonResource
{
    public function __construct($resource, ?string $class = Resource::class)
    {
        parent::__construct($resource, $class);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $data = parent::toArray($request);
        $customer = optional($this->getCustomer())->id;
        $data['reports'] = [];
        $data['status_warehouse_vn']['quantity'] = OrderPackage::query()->where('status', PackageConstant::STATUS_WAREHOUSE_VN)->where('customer_id', $customer ? '=' : '!=', $customer)->count();
        if (request()->query('status') !== PackageConstant::STATUS_WAREHOUSE_VN) {
            return $data;
        }
        $data['reports'] = $this->getCalculator($customer);
        return $data;
    }

    private function getCustomer()
    {
        $query = Customer::query();
        if(!!request()->query('customer_code'))
        {
            $query->where('code', request()->query('customer_code'));
        }
        if(!!request()->query('customer_name'))
        {
            $query->where('name', 'like', '%' . request()->query('customer_name') .'%');
        }
        if(!!request()->query('customer_phone'))
        {
            $query->where('phone_number', request()->query('customer_phone'));
        }
        return $query->first();
    }

    private function getCalculator(?string $customerId): array
    {
        $data = [];
        if(!$customerId) return $data;
        $reports = ReportOrderVN::query()->where(['customer_id' => $customerId])->get();

        $depositCost = 0;
        $orderCost = 0;
        $shipping = 0;
        $data['orders'] = [];
        foreach ($reports as $report) {
            if($report->order_amount != 0 || $report->deposit_cost != 0)
            {
                
                $data['orders'][] = [
                    'time' => $report->date_ordered,
                    'code' => $report->order_code,
                    'amount' => $report->order_amount,
                    'deposit_cost' => $report->deposit_cost,
                    'debt_cost' => $report->order_amount - $report->deposit_cost
                ];
            }
            // Hàng liên quan đế ký gửi thì không có tiền hàng và tiền tạm ứng
            if($report->order instanceof Order) {
                $depositCost += $report->deposit_cost;
                $orderCost += $report->order_amount;
            }
            $shipping += $report->shipping_cost;
        }
        $data['orders_remainder_fee'] = $orderCost - $depositCost;
        $data['total_shipping'] = $shipping;
        $data['e_wallet'] = $eWallet = optional(
            (new ReportCustomerService())->getReportCustomerCurrent($customerId)
        )->balance_amount ?? 0;
        ReportCustomer::query()->sum('balance_amount');
        $balance = $eWallet - $data['orders_remainder_fee'] - $shipping;
        $data['balance'] = $balance > 0 ? $balance : 0;
        $data['recharge'] = $balance > 0 ? 0 : abs($balance);
        return $data;
    }
}
