<?php

namespace App\Models;

use App\Models\Traits\AvatarAttribute;
use App\Models\Traits\BankRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Organization
 * @package App\Models
 *
 * @property string $id
 * @property string $name
 */

class Organization extends BaseModel
{
    use HasFactory, SoftDeletes, Filterable, HasSortDescByCreated, BankRelation, AvatarAttribute;

    public function scopeName($query)
    {
        return $query->where("name", 'like', '%'.request()->query('name').'%');
    }
}
