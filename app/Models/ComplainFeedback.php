<?php

namespace App\Models;

use App\Scopes\Traits\HasSortAscUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ComplainFeedback extends BaseModel
{
    use HasFactory, HasSortAscUuid;

    public function cause(): MorphTo
    {
        return $this->morphTo();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ComplainFeedbackAttachment::class);
    }
}
