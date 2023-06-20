<?php

namespace Database\Seeders;

use App\Constants\ActivityConstant;
use App\Models\Activity;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Services\ActivityService;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Activity::query()->truncate();
        $order = Order::query()->first();
        (new ActivityService())->setLog(
            $order,
            trans("activity.order_status_0"),
            ActivityConstant::ORDER_LOG,
            $order->id
        );
        $package = OrderPackage::query()->first();
        (new ActivityService())->setLog(
            $package,
            'Đang kiểm hàng',
            ActivityConstant::PACKAGE_LOG,
        );
    }
}
