<?php


namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class SortByCreatedAscScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $column = 'created_at';
        $isColExist = Schema::hasColumn($model->getTable(), $column);
        if (!$isColExist) {
            return;
        }
        $builder->orderBy($column);
    }
}