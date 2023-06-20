<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByCreatedAscScope;

trait HasSortAscByCreated
{
    public static function bootHasSortAscByCreated()
    {
        static::addGlobalScope(new SortByCreatedAscScope());
    }
}