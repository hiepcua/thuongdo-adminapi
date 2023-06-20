<?php


namespace App\Constants;


class ViaConstant
{
    public const KEY_THONGTIEN = 'thong_tien';
    public const KEY_GOOGLE_ADS = 'google_ads';
    public const KEY_GOOGLE = 'google';
    public const KEY_FACEBOOK_ADS = 'facebook_ads';
    public const KEY_FACEBOOK = 'facebook';
    public const KEY_ZALO = 'zalo';
    public const KEY_RESEARCH = 'research';
    public const KEY_OTHER = 'other';
    public const STATUSES = [
        self::KEY_THONGTIEN => 'Thông Tiến',
        self::KEY_GOOGLE_ADS => 'Google Quảng Cáo',
        self::KEY_GOOGLE => 'Google SEO',
        self::KEY_FACEBOOK_ADS => 'Facebook Quảng Cáo',
        self::KEY_FACEBOOK => 'Facebook Form',
        self::KEY_ZALO => 'Zalo Quảng Cáo',
        self::KEY_RESEARCH => 'KH Tự Tìm Kiếm',
        self::KEY_OTHER => 'Tự Nhiên - Khác',
    ];

    public const STATUSES_COLOR = [
        self::KEY_THONGTIEN => '#EA4336',
        self::KEY_GOOGLE_ADS => '#399CF8',
        self::KEY_GOOGLE => ColorConstant::BLACK,
        self::KEY_FACEBOOK_ADS => '#3168FF',
        self::KEY_FACEBOOK => '#3168FF',
        self::KEY_ZALO => '#3168FF',
        self::KEY_RESEARCH => '#3168FF',
        self::KEY_OTHER => '#3168FF',
    ];
}