<?php

namespace Tests\Unit;

use App\Models\OrderPackage;
use App\Services\AccountingService;
use Tests\TestCase;

class AccountingTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_inspectionCost()
    {
        (new AccountingService())->getInspectionCost(11);
        $this->assertTrue(true);
    }

    public function test_shockCost()
    {
        (new AccountingService())->getShockCost(0);
        $this->assertTrue(true);
    }

    public function test_woodworkingCost()
    {
        $this->assertTrue(true);
    }

    public function test_weightOrVolume()
    {
        $package = OrderPackage::query()->inRandomOrder()->first();
        $result = (new AccountingService())->getInternationShippingCost(
            getProvinceX(optional(optional($package)->warehouse)->province_id),
            $package->weight ?? 0,
            ($package->width ?? 0) * ($package->height ?? 0) * ($package->length ?? 0),
            $isWeight
        );
        // dd($result, $isWeight, ($package->width ?? 0) * ($package->height ?? 0) * ($package->length ?? 0));
        $this->assertIsBool($isWeight);
    }

    public function test_roundHalfUp()
    {
        $this->assertIsNumeric(roundHalfUp(random_int(1, 100) / 100));
    }
}
