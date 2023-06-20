<?php

namespace Database\Seeders;

use App\Helpers\RandomHelper;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\OrderPackageStatusTime;
use App\Models\ReportCustomer;
use App\Models\ReportOrganizationOrder;
use App\Models\ReportPackage;
use App\Services\AccountingService;
use App\Services\CustomerService;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::query()->truncate();
        OrderDetail::query()->truncate();
        ReportCustomer::query()->truncate();
        OrderPackage::query()->truncate();
        Order::factory(1)->create()->transform(
            function ($item) {
                /** @var Order $item */
                $package = OrderPackage::factory()->create(['order_code' => $item->code]);
                $details = OrderDetail::factory(3)->create(
                    ['order_id' => $item->id, 'order_package_id' => $package->id]
                );
                $amount = $details->sum('amount_cny') * $item->exchange_rate;
                $item->order_cost = $amount;
                $item->order_fee = $orderFee = (new AccountingService())->getOrderFee($amount);
                $item->packages_number = 1;
                $item->code = RandomHelper::orderCode();
                $item->save();
                OrderPackageStatusTime::query()->create(
                    ['order_package_id' => $package->id, 'key' => $package->status]
                );
                ReportPackage::query()->where('customer_id', $package->customer_id)->increment($package->status);
            }
        );
        $customerService = new CustomerService();
        /** @var Customer $customerTest */
        $customerTest = $customerService->getCustomerTest();
        /** @var ReportCustomer $report */
        $report = ReportCustomer::query()->firstOrCreate(
            ['customer_id' => $customerTest->id]
        );
        $report->status_0 = 2;
        $report->save();
        ReportCustomer::query()->create(
            ['customer_id' => $customerService->getCustomerByEmail('tungnt@gmail.com')->id]
        );
        ReportOrganizationOrder::query()->create(['organization_id' => $customerTest->organization_id]);
    }
}
