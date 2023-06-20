<?php


namespace App\Models\Traits;


use App\Models\District;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait DistrictRelation
{
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class)->select('id', 'name', 'ghn_id', 'viettel_id');
    }
}