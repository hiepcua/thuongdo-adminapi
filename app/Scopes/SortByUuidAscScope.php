<?php


namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class SortByUuidAscScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->orderBy('id');
    }
}