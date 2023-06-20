<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\Traits\HasOrganization;

class SupplierBank extends BaseModel
{
    use HasFactory, HasOrganization;
}
