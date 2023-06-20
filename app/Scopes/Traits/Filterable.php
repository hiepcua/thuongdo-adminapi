<?php


namespace App\Scopes\Traits;


use App\Scopes\FilterScope;

trait Filterable
{
    public static function bootFilterable()
    {
        static::addGlobalScope(new FilterScope());
    }
}