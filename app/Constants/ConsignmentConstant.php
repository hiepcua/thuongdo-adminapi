<?php


namespace App\Constants;


class ConsignmentConstant
{
    public const STATUS_PENDING = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_DONE = 2;
    public const STATUS_CANCEL = 3;

    public const KEY_STATUS_PENDING = 'status_'.self::STATUS_PENDING;
    public const KEY_STATUS_IN_PROGRESS = 'status_'.self::STATUS_IN_PROGRESS;
    public const KEY_STATUS_DONE = 'status_'.self::STATUS_DONE;
    public const KEY_STATUS_CANCEL = 'status_'.self::STATUS_CANCEL;

    public const STATUSES = [
        self::KEY_STATUS_PENDING => 'Chờ xử lý',
        self::KEY_STATUS_IN_PROGRESS => 'Đang xử lý',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        self::KEY_STATUS_CANCEL => 'Hủy'
    ];

    public const STATUSES_COLOR = [
        self::KEY_STATUS_PENDING => ColorConstant::STRAWBERRY,
        self::KEY_STATUS_IN_PROGRESS => ColorConstant::CARROT_ORANGE,
        self::KEY_STATUS_DONE => ColorConstant::APPLE,
        self::KEY_STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE
    ];

    public const IN_TRANSIT = ['cn-vi' => 'Trung Quốc - Việt Nam'];

    public const STATUES_SHOW_DETAILS = [
        self::KEY_STATUS_PENDING,
        self::KEY_STATUS_IN_PROGRESS,
        self::KEY_STATUS_DONE,
    ];

}