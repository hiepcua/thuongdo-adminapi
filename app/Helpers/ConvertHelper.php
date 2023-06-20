<?php


namespace App\Helpers;


class ConvertHelper
{
    public static function kilogramToGram(float $kg)
    {
        return $kg * 1000;
    }

    public static function floatToInt(float $kg): int
    {
        return round($kg);
    }

    public static function numericToVND(float $amount): string
    {
        return number_format(AccountingHelper::getCosts($amount)).'đ';
    }

    public static function numericToCNY(float $amount): string
    {
        return number_format(AccountingHelper::getCosts($amount)).'¥';
    }

    public static function arrayToString(array $array, ?string $separator = ','): string
    {
        return implode($separator, $array);
    }
}
