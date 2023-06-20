<?php


namespace App\Helpers;


use App\Constants\TimeConstant;
use Illuminate\Support\Carbon;

class TimeHelper
{
    public static function format($datetime, $format = TimeConstant::DATETIME_VI): ?string
    {
        if (!$datetime || !($datetime instanceof Carbon)) {
            return null;
        }
        return optional($datetime)->format($format);
    }
}