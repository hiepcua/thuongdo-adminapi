<?php

namespace App\Models;

use App\Models\Traits\ImageAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplainImage extends BaseModel
{
    use HasFactory, ImageAttribute;

    protected $table = 'complain_image';

}
