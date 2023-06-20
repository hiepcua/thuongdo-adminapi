<?php


namespace App\Models\Traits;


use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait OrderRelation
{
    public function order(): BelongsTo
    {
        return $this->belongTo(Order::class);
    }
}