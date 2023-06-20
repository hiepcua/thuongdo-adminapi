<?php

namespace Tests\Unit;

use App\Models\OrderPackage;
use App\Models\Transaction;
use App\Services\OrderPackageService;
use App\Services\ReportCeoService;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ReportTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_revenue()
    {
        Transaction::query()->create(
            [
                'organization_id' => getOrganization(),
                'customer_id' => 'a',
                'amount' => 20,
                'balance' => 10,
                'time' => now(),
                'status' => Arr::random(['refund', 'purchase'])
            ]
        );
        self::assertTrue(true);
    }

    public function test_shipping()
    {
        /** @var OrderPackage $package */
        $package = OrderPackage::query()->first();
        $package->update(
            $data = [
                'weight' => 10,
//                'height' => 10,
//                'length' => 10,
//                'width' => 10,
            ]
        );
        (new OrderPackageService())->changeCost($package, $data);
        $package->update($data);
        self::assertTrue(true);
    }

    public function test_categories()
    {
        (new ReportCeoService())->getChartCategories('a');
        self::assertTrue(true);
    }
}
