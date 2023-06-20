<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Relations\MorphTo;

trait OrderMorphRelation
{
    public function order(): MorphTo
    {
        return $this->morphTo();
    }
}