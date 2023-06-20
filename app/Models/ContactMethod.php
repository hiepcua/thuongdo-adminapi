<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactMethod extends BaseModel
{
    use HasFactory, HasOrganization;
}
