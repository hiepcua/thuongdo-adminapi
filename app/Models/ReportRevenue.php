<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportRevenue extends BaseModel
{
    use HasFactory, HasOrganization;

    protected $casts = ['value' => 'float'];
}
