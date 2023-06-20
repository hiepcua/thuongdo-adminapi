<?php


namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Relations\MorphTo;

trait SubjectMorph
{
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}