<?php


namespace App\Models\Traits\Filters;


trait WarehouseFilter
{
    public function scopeWarehouseId($query)
    {
        return $query->where('warehouse_id', request()->query('warehouse_id'));
    }
}