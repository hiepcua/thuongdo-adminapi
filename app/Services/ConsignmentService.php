<?php


namespace App\Services;


use App\Models\Consignment;

class ConsignmentService
{
    /**
     * @param  string  $orderId
     * @param  string  $column
     */
    public function incrementByColumn(string $orderId, string $column): void
    {
        optional(Consignment::query()->find($orderId))->increment($column);
    }
}