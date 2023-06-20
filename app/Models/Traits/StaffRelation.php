<?php


namespace App\Models\Traits;


use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait StaffRelation
{
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id', 'id');
    }
}