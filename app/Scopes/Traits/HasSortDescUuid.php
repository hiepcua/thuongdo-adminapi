<?php


namespace App\Scopes\Traits;


use App\Scopes\SortByUuidDescScope;

trait HasSortDescUuid
{
    public static function bootHasSortUuid()
    {
        static::addGlobalScope(new SortByUuidDescScope());
    }
}