<?php

namespace App\Models;

use App\Models\Traits\SubjectMorph;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderNote extends BaseModel
{
    use HasFactory, SubjectMorph;

    protected $dates = ['created_at'];
}
