<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\ActivityService;
use Tests\TestCase;

class LogTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_orderLog()
    {
        $order = Order::query()->first();
        (new ActivityService())->setOrderLog($order, 'Test', $order->id);
        $this->assertTrue(true);
    }
}
