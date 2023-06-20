<?php


namespace App\Models\Traits\Filters;


trait CodeFilter
{
    public function scopeCode($query)
    {
        return $query->where('code', request()->query('code'));
    }
}