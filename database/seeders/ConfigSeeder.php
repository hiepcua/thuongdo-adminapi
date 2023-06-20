<?php

namespace Database\Seeders;

use App\Constants\ConfigConstant;
use App\Constants\LocateConstant;
use App\Constants\TimeConstant;
use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::query()->truncate();
        Config::query()->insert(self::getData());
    }

    /**
     * Tỷ giá
     * @return array[]
     */
    public function getExchangeRate(): array
    {
        return [
            [
                'name' => 'Tỷ giá',
                'category' => 'currency',
                'key' => ConfigConstant::CURRENCY_EXCHANGE_RATE,
                'is_publish' => 1,
                'value' => 3396.08,
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * Chi phí vận chuyển
     * @return array[]
     */
    public function getDeliveries(): array
    {
        return [
            [
                'name' => 'Giá vận chuyển',
                'category' => 'delivery',
                'key' => 'delivery_type',
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        'normal' => 1000,
                        'fast' => 1500,
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * Chi phí đặt hàng
     * @return array[]
     */
    public function getOrders(): array
    {
        return [
            [
                'name' => 'Chi phí đặt hàng',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_ORDER_FEE,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        [
                            'default' => 9000
                        ],
                        self::orderValue(27000, 2000000, 3),
                        self::orderValue(2000000, 20000000, 2.5),
                        self::orderValue(20000000, 100000000, 2),
                        self::orderValue(100000000, 99999999999999999999, 1)
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ],
        ];
    }

    /**
     * Tính theo trọng lượng
     * @return array[]
     */
    public function getPackageShippingWeight(): array
    {
        return [
            [
                'name' => 'Phí vận chuyển quốc tế theo trọng lượng',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_PACKAGE_SHIPPING_WEIGHT,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        self::packageShippingValue(0, 10, 36000, 44000, 0),
                        self::packageShippingValue(10, 30, 35000, 42000, 0),
                        self::packageShippingValue(30, 100, 34000, 41000, 0),
                        self::packageShippingValue(100, 200, 32000, 38000, 0),
                        self::packageShippingValue(200, 999999, 31000, 37000, 0),
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ],
        ];
    }

    /**
     * Tính theo thể tích
     * @return array[]
     */
    public function getPackageShippingVolume(): array
    {
        return [
            [
                'name' => 'Phí vận chuyển quốc tế theo thể tích',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_PACKAGE_SHIPPING_VOLUME,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        self::packageShippingValue(0, 5, 4400000, 4800000, 0),
                        self::packageShippingValue(5, 10, 4200000, 4600000, 0),
                        self::packageShippingValue(10, 999999, 3800000, 4200000, 0)
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * Chi phí kiểm hàng
     * @return array
     */
    public function getInspections(): array
    {
        return [
            [
                'name' => 'Phí kiểm đếm',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_INSPECTION,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        [
                            'min' => 1,
                            'max' => 3,
                            'result' => 5000
                        ],
                        [
                            'min' => 3,
                            'max' => 10,
                            'result' => 3500
                        ],
                        [
                            'min' => 10,
                            'max' => 100,
                            'result' => 2000
                        ],
                        [
                            'min' => 100,
                            'max' => 500,
                            'result' => 1500
                        ],
                        [
                            'min' => 500,
                            'max' => 10000,
                            'result' => 1000
                        ],
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * @return array[]
     */
    private function getWoodworkingByWeight(): array
    {
        return [
            [
                'name' => 'Phí đóng gỗ theo trọng lượng',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_WOODWORKING_WEIGHT,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        'first' => 70000,
                        'subsequent' => 3500,
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * @return array[]
     */
    private function getWoodworkingByVolume(): array
    {
        return [
            [
                'name' => 'Phí đóng gỗ theo trọng lượng',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_WOODWORKING_VOLUME,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        [
                            'min' => 0,
                            'max' => 0.01,
                            'result' => [
                                'first' => 70000,
                                'subsequent' => 70000,
                            ]
                        ],
                        [
                            'min' => 0.01,
                            'max' => 0.1,
                            'result' => [
                                'first' => 70000,
                                'subsequent' => 15000,
                            ]
                        ],
                        [
                            'min' => 0.1,
                            'max' => 1,
                            'result' => [
                                'first' => 205000,
                                'subsequent' => 50000,
                            ]
                        ],
                        [
                            'min' => 1,
                            'max' => 9,
                            'result' => [
                                'first' => 655000,
                                'subsequent' => 655000,
                            ]
                        ]
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }


    /**
     * @return array[]
     */
    private function getCustomLevel(): array
    {
        return [
            [
                'name' => 'Triết khấu theo cập độ khách hàng',
                'category' => ConfigConstant::CATEGORY_CUSTOMER,
                'key' => ConfigConstant::CUSTOMER_LEVEL,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        [
                            'level' => 0,
                            'min' => 0,
                            'max' => 100000000,
                            'result' => [
                                'order' => 0,
                                'delivery' => 0,
                                'deposit' => 90,
                            ]
                        ],
                        [
                            'level' => 1,
                            'min' => 0,
                            'max' => 100000000,
                            'result' => [
                                'order' => 0,
                                'delivery' => 0,
                                'deposit' => 90,
                            ]
                        ],
                        [
                            'level' => 2,
                            'min' => 100000000,
                            'max' => 500000000,
                            'result' => [
                                'order' => 5,
                                'delivery' => 1,
                                'deposit' => 85,
                            ]
                        ],
                        [
                            'level' => 3,
                            'min' => 500000000,
                            'max' => 1000000000,
                            'result' => [
                                'order' => 8,
                                'delivery' => 3,
                                'deposit' => 75,
                            ]
                        ],
                        [
                            'level' => 4,
                            'min' => 1000000000,
                            'max' => 5000000000,
                            'result' => [
                                'order' => 10,
                                'delivery' => 5,
                                'deposit' => 60,
                            ]
                        ],
                        [
                            'level' => 5,
                            'min' => 5000000000,
                            'max' => 50000000000,
                            'result' => [
                                'order' => 15,
                                'delivery' => 10,
                                'deposit' => 50,
                            ]
                        ],
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    /**
     * @param $min
     * @param $max
     * @param $result
     * @return array
     */
    private function orderValue($min, $max, $result): array
    {
        return
            [
                'min' => $min,
                'max' => $max,
                'result' => $result
            ];
    }

    /**
     * @param $min
     * @param $max
     * @param $hanoi
     * @param $hcm
     * @param $hp
     * @return array
     */
    private function packageShippingValue($min, $max, $hanoi, $hcm, $hp): array
    {
        return [
            'min' => $min,
            'max' => $max,
            'result' => [
                LocateConstant::HANOI => $hanoi,
                LocateConstant::HCM => $hcm,
                LocateConstant::HP => $hp,
            ]
        ];
    }

    /**
     * @param $value
     * @return false|mixed|string
     */
    private function prepareValue($value)
    {
        $value = is_array($value) ? json_encode($value) : $value;
        return $value;
    }

    private function getShockProof(): array
    {
        return [
            [
                'name' => 'Phí chống sốc',
                'category' => ConfigConstant::CATEGORY_SERVICE,
                'key' => ConfigConstant::SERVICE_SHOCK_PROOF,
                'is_publish' => 1,
                'value' => self::prepareValue(
                    [
                        'first' => 8,
                        'subsequent' => 1.5,
                    ]
                ),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    private function getInsurance(): array
    {
        return [
            [
                'name' => 'Bảo hiểm',
                'category' => 'insurance',
                'key' => ConfigConstant::SERVICE_INSURANCE,
                'is_publish' => 1,
                'value' => 5,
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    private function getCarriers(): array
    {
        return [
            [
                'name' => 'Đơn vị vận chuyển',
                'category' => ConfigConstant::CARRIERS,
                'key' => ConfigConstant::CARRIERS,
                'is_publish' => 0,
                'value' => $this->prepareValue(json_decode(env('CARRIER_JSON', '[]'), true)),
                'id' => getUuid(),
                'created_at' => date(TimeConstant::DATETIME)
            ]
        ];
    }

    private function getData(): array
    {
        return array_merge(
            self::getExchangeRate(),
            self::getPackageShippingWeight(),
            self::getPackageShippingVolume(),
            self::getOrders(),
            self::getDeliveries(),
            self::getInspections(),
            self::getWoodworkingByWeight(),
            self::getWoodworkingByVolume(),
            self::getCustomLevel(),
            self::getShockProof(),
            self::getInsurance(),
            self::getCarriers()
        );
    }
}
