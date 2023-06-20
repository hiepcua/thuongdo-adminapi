<?php

namespace App\Models;

use App\Models\Traits\ImageAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetailImage extends BaseModel
{
    use HasFactory, ImageAttribute;

    protected $table = 'order_detail_image';
}
