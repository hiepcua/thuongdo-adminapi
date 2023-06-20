<?php

namespace App\Models;

use App\Models\Traits\SubjectMorph;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetailNote extends BaseModel
{
    use HasFactory, SubjectMorph, HasSortDescByCreated;

    protected $dates = ['created_at'];

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
