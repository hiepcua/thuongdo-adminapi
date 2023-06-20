<?php


namespace App\Constants;


class LocateConstant
{
    public const COUNTRY_VI = 'vi';
    public const COUNTRY_CN = 'cn';
    public const HANOI = 'hanoi';
    public const HCM = 'hochiminh';
    public const HP = 'haiphong';

    public const HANOI_HCM_HP = [
        self::HANOI => 'Hà Nội',
        self::HCM => 'Hồ Chí Minh',
        self::HP => 'Hải Phòng'
    ];

    public const PROVINCE_ID_X = 'x';
}