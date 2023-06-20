<?php


namespace App\Models\Traits;


use App\Models\Province;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BankRelation
{
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}