<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Module extends BaseModel
{
    use HasFactory;

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
