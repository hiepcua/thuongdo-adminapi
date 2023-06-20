<?php


namespace App\Constants;


class TransactionConstant
{
    public const STATUS_DEPOSIT = 'deposit';
    public const STATUS_WITHDRAWAL = 'withdrawal';
    public const STATUS_PURCHASE = 'purchase';
    public const STATUS_REFUND = 'refund';
    public const STATUS_EXCHANGE = 'exchange';

    public const STATUSES = [
        self::STATUS_DEPOSIT => 'Nạp tiền',
        self::STATUS_PURCHASE => 'Thanh toán',
        self::STATUS_WITHDRAWAL => 'Rút tiền',
        self::STATUS_REFUND => 'Hoàn lại',
        self::STATUS_EXCHANGE => 'Đổi tiền',
    ];

    public const STATUSES_COLOR = [
        self::STATUS_DEPOSIT => ColorConstant::GREEN,
        self::STATUS_PURCHASE => ColorConstant::RED,
        self::STATUS_WITHDRAWAL => ColorConstant::STRAWBERRY,
        self::STATUS_REFUND => ColorConstant::CARROT_ORANGE,
        self::STATUS_EXCHANGE => ColorConstant::NAVY_BLUE,
    ];
}
