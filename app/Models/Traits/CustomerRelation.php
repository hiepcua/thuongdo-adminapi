<?php


namespace App\Models\Traits;


use App\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CustomerRelation
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}