<?php


namespace App\Constants;


use App\Models\ComplainStatusTime;
use App\Models\ConsignmentStatusTime;
use App\Models\OrderPackageStatusTime;

class StatusConstant
{
    public const STATUSES = ['Chưa xác định', 'Đang xử lý', 'Đã xử lý'];
    public const STATUSES_COLOR = [ColorConstant::WHITE, ColorConstant::CARROT_ORANGE, ColorConstant::GREEN];

    public const TABLE_STATUS_TIMES = [
        'order_package_id' => OrderPackageStatusTime::class,
        'consignment_id' => ConsignmentStatusTime::class,
        'complain_id' => ComplainStatusTime::class,
    ];
}