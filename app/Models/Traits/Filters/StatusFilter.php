<?php


namespace App\Models\Traits\Filters;


trait StatusFilter
{
    public function scopeStatus($query)
    {
        return $query->where('status', request()->query('status'));
    }
}