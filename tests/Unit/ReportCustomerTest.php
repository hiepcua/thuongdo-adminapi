<?php

namespace Tests\Unit;

use App\Services\ReportCustomerService;
use Tests\TestCase;

class ReportCustomerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_checkNewCustomer()
    {
        (new ReportCustomerService())->isNewCustomerByCustomerId('4db1c2e7-3436-41ee-b994-b384f81c17fa');
        $this->assertTrue(true);
    }
}
