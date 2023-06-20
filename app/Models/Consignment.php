<?php

namespace App\Models;

use App\Models\Traits\CustomerDeliveryRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consignment extends BaseModel
{
    use HasFactory, CustomerDeliveryRelation;

    protected $dates = [
        'date_ordered'
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_vi', 'id');
    }
}
