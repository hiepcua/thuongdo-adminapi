<?php


namespace App\Constants;


class ServiceConstant
{
    public const DELIVERY_NORMAL = 'normal';
    public const DELIVERY_FAST = 'fast';
    public const DELIVERIES = [self::DELIVERY_NORMAL, self::DELIVERY_FAST];
}