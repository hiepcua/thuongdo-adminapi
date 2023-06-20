<?php


namespace App\Constants;

use App\Models\Category;
use App\Models\Transporter;

class ActivityConstant
{
    public const ORDER_LOG = 'order_log';
    public const PACKAGE_LOG = 'package_log';
    public const CONSIGNMENT_LOG = 'consignment_log';

    public const PACKAGE_STATUS = 'order_package_status';
    public const PACKAGE_NOTE = 'order_package_note';
    public const PACKAGE_INFO = 'order_package_info';
    public const PACKAGE_WEIGHT = 'order_package_weight';

    public const PACKAGE_PROPERTIES = [
        'code_po' => 'Mã đặt hàng',
        'bill_code' => 'Mã vận đơn',
        'transporter_id' => 'Hãng vận chuyển',
        'category_id' => 'Danh mục',
        'transporter' => 'Hãng vận chuyển',
        'package_number' => 'Số kiện',
        'china_shipping_cost' => 'Phí vận chuyển nội địa',
        'delivery_type' => 'Loại vận chuyển'
    ];

    public const PACKAGE_MODELS = [
        'transporter_id' => Transporter::class,
        'category_id' => Category::class,
    ];

    public const DELIVERY_STATUS = 'delivery_status';
    public const DELIVERY_PACKAGE = 'delivery_package';
}