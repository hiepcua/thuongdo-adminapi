<?php


namespace App\Constants;


class FineConstant
{
    public const TYPE_ORDER = 'order';

    public const TYPES = [
        self::TYPE_ORDER => 'Đơn hàng',
        'wrong_order' => 'Đặt hàng sai',
        'bill' => 'Mã vận đơn',
        'other' => 'Khác'
    ];

    public const KEY_STATUS_PENDING = 'pending';
    public const KEY_STATUS_DONE = 'done';
    public const KEY_STATUS_CANCEL = 'cancel';

    public const STATUSES = [
        self::KEY_STATUS_PENDING => 'Chờ xử lý',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        self::KEY_STATUS_CANCEL => 'Đã hủy',
    ];

    public const STATUSES_COLOR = [
        self::KEY_STATUS_PENDING => ColorConstant::CARROT_ORANGE,
        self::KEY_STATUS_DONE => ColorConstant::GREEN,
        self::KEY_STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE,
    ];
}