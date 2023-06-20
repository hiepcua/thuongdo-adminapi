<?php


namespace App\Models\Traits;


use App\Models\District;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait WardRelation
{
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class)->select('id', 'name', 'code');
    }
}