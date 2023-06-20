<?php

namespace App\Models;

use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Scopes\Traits\Filterable;

class Department extends BaseModel
{
    use HasFactory, HasRoles, Filterable, HasSortDescByCreated, HasOrganization;

    protected $guard_name = 'api';


    public function scopeName($query)
    {
        return $query->where("name", 'like', '%'.request()->query('name').'%');
    }
}
