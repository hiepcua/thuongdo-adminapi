<?php


namespace App\Constants;


class OrderConstant
{
    public const KEY_STATUS_WAITING_QUOTE = 'status_0';
    public const KEY_STATUS_WAITING_DEPOSIT = 'status_1';
    public const KEY_STATUS_DEPOSITED = 'status_2';
    public const KEY_STATUS_ORDERED = 'status_3';
    public const KEY_STATUS_DONE = 'status_4';
    public const KEY_STATUS_RECONFIRM = 'status_5';
    public const KEY_STATUS_CANCEL = 'status_6';
    public const KEY_STATUS_ORDERING= 'status_7';
    public const KEY_STATUS_WAIT_TO_PAY= 'status_8';
    public const STATUSES = [
        self::KEY_STATUS_WAITING_QUOTE => 'Chờ báo giá',
        self::KEY_STATUS_WAITING_DEPOSIT => 'Chờ đặt cọc',
        self::KEY_STATUS_DEPOSITED => 'Đã đặt cọc',
        self::KEY_STATUS_ORDERING => 'Đang đặt hàng',
        self::KEY_STATUS_WAIT_TO_PAY => 'Chờ thanh toán',
        self::KEY_STATUS_ORDERED => 'Đặt hàng',
        self::KEY_STATUS_DONE => 'Đã hoàn thành',
        self::KEY_STATUS_RECONFIRM => 'Cần xác nhận lại',
        self::KEY_STATUS_CANCEL => 'Đã hủy'
    ];

    public const STATUSES_KEYS = [
        self::KEY_STATUS_WAITING_QUOTE,
        self::KEY_STATUS_WAITING_DEPOSIT,
        self::KEY_STATUS_DEPOSITED,
        self::KEY_STATUS_ORDERING,
        self::KEY_STATUS_WAIT_TO_PAY,
        self::KEY_STATUS_ORDERED,
        self::KEY_STATUS_DONE,
        self::KEY_STATUS_RECONFIRM,
        self::KEY_STATUS_CANCEL
    ];

    public const STATUSES_COLOR = [
        self::KEY_STATUS_WAITING_QUOTE => ColorConstant::STRAWBERRY,
        self::KEY_STATUS_WAITING_DEPOSIT => ColorConstant::CARROT_ORANGE,
        self::KEY_STATUS_DEPOSITED => ColorConstant::APPLE,
        self::KEY_STATUS_ORDERING => '#0097e6',
        self::KEY_STATUS_WAIT_TO_PAY => '#8c7ae6',
        self::KEY_STATUS_ORDERED => ColorConstant::NAVY_BLUE,
        self::KEY_STATUS_DONE => ColorConstant::TIFFANY_BLUE,
        self::KEY_STATUS_RECONFIRM => ColorConstant::RED,
        self::KEY_STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE
    ];

    public const DELIVERY_NORMAL = 'normal';
    public const DELIVERY_FAST = 'fast';

    public const DELIVERIES_TEXT = [
        self::DELIVERY_NORMAL => 'Thường',
        self::DELIVERY_FAST => 'Nhanh'
    ];

    public const ECOMMERCE= [
        'taobao.com' => 'Taobao.com',
        'tmall.com' => 'Tmall.com',
        '1688.com' => '1688.com',
    ];

    public const SORT_TIME  = [
        'desc' => 'Mới nhất',
        'asc' => 'Cũ nhất',
    ];

    public const SORT_COST  = [
        'desc' => 'Cao nhất',
        'asc' => 'Thấp nhất',
    ];

    public const TOOL = [
        'Công cụ',
        'Website',
    ];

    public const ORDER_TYPES  = [
        'Chờ thanh toán',
        'Đã Thanh toán',
    ];

    public const TAX  = [
        'Không Khai thuế',
        'Khai thuế'
    ];

    public static function getStatusKeys(): array
    {
        return array_keys(self::STATUSES);
    }
}