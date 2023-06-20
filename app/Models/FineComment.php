<?php

namespace App\Models;

use App\Models\Traits\StaffRelation;
use App\Scopes\Traits\HasSortAscUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FineComment extends BaseModel
{
    use HasFactory, StaffRelation, HasSortAscUuid;

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
