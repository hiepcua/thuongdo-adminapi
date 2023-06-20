<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByCreatedDescScope;
use App\Scopes\SortByCreatedAscScope;

trait HasSortDescByCreated
{
    public static function bootHasSortDescByCreated()
    {
        static::addGlobalScope(new SortByCreatedDescScope());
    }
}