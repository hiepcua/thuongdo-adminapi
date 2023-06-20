<?php


namespace App\Models\Traits\Filters;


use App\Services\FilterService;

trait CreatedAtFilter
{
    public function scopeCreatedAt($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('created_at'), 'created_at');
    }
}