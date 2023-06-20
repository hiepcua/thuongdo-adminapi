<?php

namespace App\Models;

use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeliveryNote extends BaseModel
{
    use HasFactory, HasSortDescByCreated;

    public function staff(): MorphTo
    {
        return $this->morphTo('cause');
    }
}
