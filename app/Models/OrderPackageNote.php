<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderPackageNote extends BaseModel
{
    use HasFactory;

    public function staff(): MorphTo
    {
        return $this->morphTo('cause');
    }
}
