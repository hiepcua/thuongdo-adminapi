<?php


namespace App\Models\Traits;


use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait WarehouseRelation
{
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}