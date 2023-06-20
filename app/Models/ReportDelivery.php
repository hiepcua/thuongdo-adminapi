<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportDelivery extends BaseModel
{
    use HasFactory, HasOrganization;
}
