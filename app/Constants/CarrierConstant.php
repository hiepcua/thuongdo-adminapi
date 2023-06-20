<?php


namespace App\Constants;


class CarrierConstant
{
    public const ENDPOINT_GHTK_PRICE = '/services/shipment/fee';

    public const ENDPOINT_GHN_PRICE = '/shipping-order/fee';

    public const ENDPOINT_VIETTEL_LOGIN = '/user/Login';
    public const ENDPOINT_VIETTEL_PRICE = '/order/getPrice';

    public const GHN = 'ghn';
    public const GHTK = 'ghtk';
    public const VIETTEL = 'viettel';

    public const CARRIERS = [self::GHTK, self::GHN, self::VIETTEL];
    public const CARRIERS_HAS_DELIVERY_TYPE = [self::GHTK, self::VIETTEL];
}