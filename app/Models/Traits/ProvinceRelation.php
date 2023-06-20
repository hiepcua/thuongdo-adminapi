<?php


namespace App\Models\Traits;


use App\Models\Province;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ProvinceRelation
{
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class)->select('id', 'name', 'ghn_id', 'viettel_id');
    }
}