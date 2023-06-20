<?php


namespace App\Constants;


class ComplainConstant
{
    public const STATUS_DONE_INDEX = 3;
    public const KEY_STATUS_PENDING = 'status_0';
    public const KEY_STATUS_PROCESS = 'status_1';
    public const KEY_STATUS_PROCESSED = 'status_2';
    public const KEY_STATUS_DONE = 'status_3';
    public const KEY_STATUS_CANCEL = 'status_4';
    public const STATUSES = [
        self::KEY_STATUS_PENDING => 'Chờ xử lý',
        self::KEY_STATUS_PROCESS => 'Đang xử lý',
        self::KEY_STATUS_PROCESSED => 'Đã xử lý',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        self::KEY_STATUS_CANCEL => 'Đã hủy'
    ];
    public const STATUSES_COLOR = [
        self::KEY_STATUS_PENDING => ColorConstant::STRAWBERRY,
        self::KEY_STATUS_PROCESS => ColorConstant::CARROT_ORANGE,
        self::KEY_STATUS_PROCESSED => ColorConstant::APPLE,
        self::KEY_STATUS_DONE => ColorConstant::RED,
        self::KEY_STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE
    ];

    public const NOTE_TYPES = [
        NoteConstant::TYPE_PRIVATE => 'Nội bộ',
        NoteConstant::TYPE_PUBLIC => 'Khách hàng',
    ];
}