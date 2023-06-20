<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierType extends BaseModel
{
    use HasFactory, HasOrganization;
}
