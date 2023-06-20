<?php


namespace App\Helpers;


use App\Constants\ResourceConstant;

class PaginateHelper
{
    public static function getLimit(): int
    {
        return (int)(request()->input('limit') ?? ResourceConstant::LIST_LIMIT);
    }

    public static function getPerPage(): int
    {
        return (int)(request()->input('per_page') ?? ResourceConstant::PER_PAGE);
    }
}