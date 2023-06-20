<?php

namespace App\Models;

use App\Models\Traits\StaffRelation;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends BaseModel
{
    use HasFactory, StaffRelation, HasSortDescByCreated;

    protected $dates = ['created_at'];

}
