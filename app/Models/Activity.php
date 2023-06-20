<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends BaseModel
{
    use HasFactory, HasOrganization, HasSortDescByCreated;

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    public function causer(): MorphTo
    {
        return $this->morphTo('causer');
    }
}
