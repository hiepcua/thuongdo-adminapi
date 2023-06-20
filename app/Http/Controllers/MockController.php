<?php

namespace App\Http\Controllers;

use App\Constants\ActivityConstant;
use App\Constants\CustomerConstant;
use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Constants\TimeConstant;
use App\Constants\TransactionConstant;
use App\Helpers\ConvertHelper;
use App\Models\Activity;
use App\Models\Complain;
use App\Models\ComplainStatusTime;
use App\Models\Consignment;
use App\Models\CustomerWithdrawal;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\OrderPackageStatusTime;
use App\Models\OrderStatusTime;
use App\Models\Organization;
use App\Models\ReportComplain;
use App\Models\ReportCustomer;
use App\Models\ReportOrderVN;
use App\Models\ReportPackage;
use App\Services\AccountingService;
use App\Services\ActivityService;
use App\Services\OrderPackageService;
use App\Services\ReportCustomerService;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class MockController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function handle(): JsonResponse
    {
        // TODO: Handle
        return resSuccess();
    }

    /**
     * @param  Order  $order
     * @return JsonResponse
     */
    public function orderPackage(Order $order): JsonResponse
    {
        $details = $order->details;
        OrderPackage::query()->where('order_id', $order->id)->get()->each(
            function ($package) {
                ReportPackage::query()->where('customer_id', $package->customer_id)->decrement($package->status);
                (new ReportCustomerService())->decrementByKey(CustomerConstant::KEY_REPORT_PACKAGE, $package->customer_id);
                $package->delete();
            }
        );
        Activity::query()->where('object_id', $order->id)->delete();
        $packageNumber = (clone $details)->count();
        (new ReportCustomerService())->incrementByKey(CustomerConstant::KEY_REPORT_PACKAGE, $order->customer_id, $packageNumber);
        $ids = OrderPackage::factory($packageNumber)->create(['order_id' => $order->id])->pluck('id')->all();
        $order->packages_number = count($ids);
        $order->save();
        foreach ($details as $key => $detail) {
            $detail->order_package_id = $ids[$key];
            $detail->save();
            OrderPackageStatusTime::query()->create(
                ['key' => 'package_status_0', 'time' => now(), 'order_package_id' => $ids[$key]]
            );
            /** @var OrderPackage $package */
            $package = OrderPackage::query()->find($ids[$key]);
            $package->customer_id = $order->customer_id;
            $package->customer_delivery_id = $order->customer_delivery_id;
            $package->order_code = $order->code;
            $package->quantity = $detail->quantity;
            $package->order_cost = $orderCost = $detail->amount_cny * $order->exchange_rate;
            $package->insurance_cost = $insuranceCost = $package->is_order ? 0 : ($package->is_insurance ? (new AccountingService(
            ))->getInsuranceCost($orderCost) : 0);
            $package->international_shipping_cost = (new AccountingService())->getInternationShippingCost(
                getProvinceX(optional($package->warehouse)->province_id),
                $package->weight,
                $package->volume
            );

            $package->inspection_cost = (new AccountingService())->getInspectionCost(
                OrderDetail::query()->where('order_package_id', $package->id)->count()
            );
            $package->save();
            ReportPackage::query()->where('customer_id', $package->customer_id)->increment($package->status);
            (new ActivityService())->setLog(
                $package,
                'Chờ xử lý',
                ActivityConstant::PACKAGE_LOG,
                $order->id
            );
        }

        return resSuccess();
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function orderPackageUpdateStatus(string $id): JsonResponse
    {
        $status = $this->getStatusKey(request()->input('status'));
        OrderPackageStatusTime::query()->firstOrCreate(
            ['order_package_id' => $id, 'key' => $status]
        );
        /** @var OrderPackage $package */
        $package = OrderPackage::query()->findOrFail($id);
        $oldStatus = $package->status;
        if ($oldStatus === PackageConstant::STATUS_WAREHOUSE_VN && $status === PackageConstant::STATUS_WAREHOUSE_VN) {
            return resSuccess();
        }
        $package->update(['status' => $status]);
        $reportPackage = ReportPackage::query()->firstOrCreate(['customer_id' => $package->customer_id]);
        (clone $reportPackage)->increment($status);
        (clone $reportPackage)->decrement($oldStatus);
        (new ActivityService())->setLog(
            $package,
            isset(PackageConstant::STATUSES[$status]) ? PackageConstant::STATUSES[$status] : 'Chờ xử lý',
            ActivityConstant::PACKAGE_LOG,
            $package->order_id
        );
        (new OrderPackageService())->reportOrderVN($package, $oldStatus);
        return resSuccess();
    }


    /**
     * @param  OrderPackage  $package
     * @return float
     */
    private function getServicesCost(OrderPackage $package): float
    {
        return $package->inspection_cost + $package->insurance_cost + $package->woodworking_cost + $package->shock_proof_cost + $package->storage_cost;
    }

    private function getShippingCost(OrderPackage $package): float
    {
        return $package->international_shipping_cost + $package->china_shipping_cost;
    }

    /**
     * @param  Order  $order
     * @return JsonResponse
     */
    public function orderUpdateStatus(Order $order): JsonResponse
    {
        $report = ReportCustomer::query()->where('customer_id', $order->customer_id);
        (clone $report)->decrement($order->status);
        (clone $report)->increment($status = $this->getStatusKey(request()->input('status')));
        $order->status = $status;
        $date = date(TimeConstant::DATETIME);
        if($status == OrderConstant::KEY_STATUS_DONE) {
            $order->date_done = $date;
        }
        if($status == OrderConstant::KEY_STATUS_DEPOSITED) {
            $order->date_purchased = $date;
        }
        if($status == OrderConstant::KEY_STATUS_WAITING_DEPOSIT) {
            $order->date_quotation = $date;
        }
        $order->save();
        OrderStatusTime::query()->firstOrCreate(['order_id' => $order->id])->update([$status => $date]);

        return resSuccess();
    }

    /**
     * @param  Complain  $complain
     * @return JsonResponse
     */
    public function complainUpdateStatus(Complain $complain): JsonResponse
    {
        $complain->status = $this->getStatusKey(request()->input('status'));
        $complain->save();
        ComplainStatusTime::query()->create(
            ['complain_id' => $complain->id, 'key' => $this->getStatusKey($complain->status)]
        );
        ReportComplain::query()->where('customer_id', $complain->customer_id)->increment($this->getStatusKey(request()->input('status')));
        return resSuccess();
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusKey($status): string
    {
        return "status_$status";
    }

    public function depositMoney(string $customerId): JsonResponse
    {
        $report = $this->getReportCustomer($customerId);
        $report->balance_amount = request()->input('amount');
        $report->deposited_amount += request()->input('amount');
        $report->save();
        return resSuccess();
    }

    private function getReportCustomer(string $customerId)
    {
        return ReportCustomer::query()->firstOrCreate(['customer_id' => $customerId]);
    }

    public function consignmentStatus(string $id): JsonResponse
    {
        Consignment::query()->findOrFail($id)->update(['status' => request()->input('status')]);
        return resSuccess();
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function withdrawalStatus(string $id): JsonResponse
    {
        $withdrawal = CustomerWithdrawal::query()->findOrFail($id);
        $withdrawal->update(['status' => $status = request()->input('status')]);
        return resSuccess();
    }
}
