<?php


namespace App\Models\Traits;


use App\Models\OrderPackage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait OrderPackageRelation
{
    public function orderPackage(): BelongsTo
    {
        return $this->belongsTo(OrderPackage::class);
    }
}