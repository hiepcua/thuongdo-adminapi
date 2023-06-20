<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DeliveryOrder extends BaseModel
{
    use HasFactory;

    public function order(): MorphTo
    {
        return $this->morphTo();
    }
}
