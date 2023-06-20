<?php

namespace App\Models;

use App\Models\Traits\Filters\CreatedAtFilter;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\StaffRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Fine
 * @package App\Models
 *
 * @property string $id
 * @property string $status
 * @property string $amount
 * @property string $user_id
 * @property string $cause_id
 * @property string $type
 */

class Fine extends BaseModel
{
    use HasFactory, StaffRelation, Filterable, CreatedAtFilter, HasSortDescByCreated, StatusFilter;

    protected $casts = [
        'amount' => 'float'
    ];

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function cause(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'cause_id', 'id');
    }

    public function scopeOrderCode($query)
    {
        return $query->where('order_code', request()->query('order_code'));
    }

    public function scopeCodePo($query)
    {
        return $query->whereHas('source', function($q) {
           $q->where('code_po', request()->query('code_po'));
        });
    }

    public function scopeUserId($query)
    {
        return $query->where('user_id', request()->query('user_id'));
    }

    public function scopeCauseId($query)
    {
        return $query->where('cause_id', request()->query('cause_id'));
    }

    public function scopeType($query)
    {
        return $query->where('type', request()->query('type'));
    }
}
