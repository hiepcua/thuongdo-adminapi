<?php


namespace App\Constants;


class TimeConstant
{
    public const DATE = 'Y-m-d';
    public const TIME = 'H:i:s';
    public const HOUR_MINUTE = 'H:i';
    public const DATETIME = self::DATE.' '.self::TIME;

    public const DATE_VI = 'd-m-Y';
    public const DATETIME_VI = self::DATE_VI.' '.self::TIME;
    public const DATETIME_BY_HI_DAY= 'H:i '. self::DATE_VI;
    public const DATETIME_BY_DAY_HI= self::DATE_VI . ' H:i';

    public const DATE_MAX = '2099-01-01';
}