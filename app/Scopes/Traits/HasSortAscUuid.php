<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByUuidAscScope;

trait HasSortAscUuid
{
    public static function bootHasSortUuid()
    {
        static::addGlobalScope(new SortByUuidAscScope());
    }
}