<?php


namespace App\Constants;


class PackageConstant
{
    public const INDEX_STATUS_WAITING_CODE = 14;
    public const STATUS_PENDING = 'status_0';
    public const STATUS_RECONFIRM = 'status_1';
    public const STATUS_IN_GUANGZHOU = 'status_2';
    public const STATUS_ON_THE_WAY_PINGXIANG = 'status_3';
    public const STATUS_ON_THE_WAY_CHINA = 'status_4';
    public const STATUS_IN_CHINA = 'status_5';
    public const STATUS_ON_THE_WAY_VN = 'status_6';
    public const STATUS_IN_PINGXIANG = 'status_7';
    public const STATUS_CHECKING_GOODS = 'status_8';
    public const STATUS_LOADING_GOODS = 'status_9';
    public const STATUS_WAREHOUSE_VN = 'status_10';
    public const STATUS_ON_THE_WAY_HCM = 'status_11';
    public const STATUS_CHECKING_GOODS_HCM = 'status_12';
    public const STATUS_ON_THE_WAY_HP = 'status_13';
    public const STATUS_WAITING_CODE = 'status_14';
    public const STATUS_IN_PROGRESS = 'status_15';
    public const STATUS_RECEIVED_GOODS = 'status_16';
    public const STATUS_CANCEL = 'status_17';
    public const STATUSES = [
        self::STATUS_PENDING => 'Chờ xử lý',
        self::STATUS_RECONFIRM => 'Xác nhận lại',
        self::STATUS_IN_GUANGZHOU => 'Đang ở kho QC',
        self::STATUS_ON_THE_WAY_PINGXIANG => 'Đang về Bằng Tường',
        self::STATUS_ON_THE_WAY_CHINA => 'Đang về kho TQ',
        self::STATUS_IN_CHINA => 'Đã đến kho TQ',
        self::STATUS_ON_THE_WAY_VN => 'Đang về VN',
        self::STATUS_IN_PINGXIANG => 'Đến kho Bằng Tường',
        self::STATUS_CHECKING_GOODS => 'Đang kiểm hàng',
        self::STATUS_LOADING_GOODS => 'Đang chuyển hàng',
        self::STATUS_WAREHOUSE_VN => 'Đến kho VN',
        self::STATUS_ON_THE_WAY_HCM => 'Đang về Tp.HCM',
        self::STATUS_CHECKING_GOODS_HCM => 'Đang kiểm hàng Tp.HCM',
        self::STATUS_ON_THE_WAY_HP => 'Đang về Hải Phòng',
        self::STATUS_WAITING_CODE => 'Chờ COD',
        self::STATUS_IN_PROGRESS => 'Đang giao hàng',
        self::STATUS_RECEIVED_GOODS => 'Đã nhận hàng',
        self::STATUS_CANCEL => 'Đã hủy',
    ];

    public const STATUSES_COLOR = [
        self::STATUS_PENDING => ColorConstant::STRAWBERRY,
        self::STATUS_RECONFIRM => ColorConstant::CARROT_ORANGE,
        self::STATUS_IN_GUANGZHOU => ColorConstant::APPLE,
        self::STATUS_ON_THE_WAY_PINGXIANG => ColorConstant::NAVY_BLUE,
        self::STATUS_ON_THE_WAY_CHINA => ColorConstant::TIFFANY_BLUE,
        self::STATUS_IN_CHINA => ColorConstant::RED,
        self::STATUS_ON_THE_WAY_VN => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_IN_PINGXIANG => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_CHECKING_GOODS => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_LOADING_GOODS => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_WAREHOUSE_VN => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_ON_THE_WAY_HCM => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_CHECKING_GOODS_HCM => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_ON_THE_WAY_HP => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_WAITING_CODE => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_IN_PROGRESS => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_RECEIVED_GOODS => ColorConstant::PRUSSIAN_BLUE,
        self::STATUS_CANCEL => ColorConstant::PRUSSIAN_BLUE,
    ];

    public const STATUES_SHOW_DETAILS = [
        self::STATUS_PENDING,
        self::STATUS_ON_THE_WAY_CHINA,
        self::STATUS_IN_CHINA,
        self::STATUS_ON_THE_WAY_VN,
        self::STATUS_WAREHOUSE_VN,
        self::STATUS_RECEIVED_GOODS,
    ];

    public const TYPES = [
        'order' => 'Hàng order',
        'consignment' => 'Hàng ký gửi',
        'unconfirmed' => 'Chờ xác nhận',
    ];

    public const MODIFIES = [
        'Chưa có mã vận đơn',
        'Chưa có mã đặt hàng',
        'Chưa có hãng vận chuyển',
        'Có thông tin khách hàng',
        'Cập nhật bằng addon',
        'Hàng đủ điều kiện kê khai',
        'Đơn đủ điều kiện',
        'Đơn nhiều kiện'
    ];

    public const REQUESTS = [
        'Kiểm hàng',
        'Đóng gỗ',
        'Bảo hiểm',
        'Yêu cầu giao hàng',
        'Hàng chuyển chậm'
    ];

    public const STATUSES_FILTER = [
        'Kiện chưa về VIỆT NAM',
        'Kiện hàng khách chưa nhận'
    ];

    public const UNITS = [
        'weight' => 'Kg',
        'volume' => 'M3'
    ];

    public const TYPE_NOTE_NOTE = 'note';
    public const TYPE_NOTE_ORDER = 'note_ordered';

    public const TYPES_NOTE = [
        self::TYPE_NOTE_ORDER => 'Ghi chú cho Khách hàng',
        self::TYPE_NOTE_NOTE => 'Ghi chú nội bộ'
    ];

    public const DELIVERIES = [
        'Chưa nhận hàng',
        'Đã nhận hàng',
    ];

}