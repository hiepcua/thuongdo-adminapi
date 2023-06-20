<?php

namespace Tests\Unit;

use App\Services\CustomerService;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $count = (new CustomerService())->getCustomerNumberByYearAndMonth();
        $this->assertIsNumeric($count);
    }
}
