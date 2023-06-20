<?php

namespace Database\Seeders;

use App\Constants\LocateConstant;
use App\Models\Transporter;
use Illuminate\Database\Seeder;

class TransporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transporter::query()->truncate();
        Transporter::query()->insert(
            [
                [
                    'id' => getUuid(),
                    'name' => 'Giao hàng nhanh',
                    'is_delivery_type' => false,
                    'is_get_delivery_price' => true,
                    'short_name' => 'ghn',
                    'order' => 1,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_VI
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Viettel',
                    'short_name' => 'viettel',
                    'is_get_delivery_price' => true,
                    'is_delivery_type' => true,
                    'order' => 2,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_VI
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Giao hàng tiết kiệm',
                    'short_name' => 'ghtk',
                    'is_get_delivery_price' => true,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_VI
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Ship nội thành',
                    'short_name' => 'local',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => false,
                    'order' => 4,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_VI
                ],
                [
                    'id' => getUuid(),
                    'name' => 'Xe khách',
                    'short_name' => 'xe-khach',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => false,
                    'order' => 5,
                    'has_children' => 1,
                    'country' => LocateConstant::COUNTRY_VI
                ],
                [
                    'id' => getUuid(),
                    'name' => '邮政包裹/平邮(youzhengguonei)',
                    'short_name' => 'youzhengguonei',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '顺丰(shunfeng)',
                    'short_name' => 'shunfeng',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => 'EMS经济快递(ems)',
                    'short_name' => 'ems',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '申通(shentong)',
                    'short_name' => 'shentong',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '圆通(yuantong)',
                    'short_name' => 'yuantong',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '中通(zhongtong)',
                    'short_name' => 'zhongtong',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '韵达(yunda)',
                    'short_name' => 'yunda',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '天天(tiantian)',
                    'short_name' => 'tiantian',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '速尔(suer)',
                    'short_name' => 'suer',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '优速(youshuwuliu)',
                    'short_name' => 'youshuwuliu',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '京东(jd)',
                    'short_name' => 'jd',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => 'DHL(dhl)',
                    'short_name' => 'dhl',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '中邮(zhongyouwuliu)',
                    'short_name' => 'zhongyouwuliu',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '天地华宇(tiandihuayu)',
                    'short_name' => 'tiandihuayu',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '百世(baishiwuliu)',
                    'short_name' => 'baishiwuliu',
                    'is_get_delivery_price' => false,
                    'is_delivery_type' => true,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '安能(anneng)',
                    'short_name' => 'anneng',
                    'is_delivery_type' => true,
                    'is_get_delivery_price' => false,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '德邦(debang)',
                    'short_name' => 'debang',
                    'is_delivery_type' => true,
                    'is_get_delivery_price' => false,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '国通(guotong)',
                    'short_name' => 'guotong',
                    'is_delivery_type' => true,
                    'is_get_delivery_price' => false,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '壹米滴答(yimidida)',
                    'short_name' => 'yimidida',
                    'is_delivery_type' => true,
                    'is_get_delivery_price' => false,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
                [
                    'id' => getUuid(),
                    'name' => '其他(qita)',
                    'short_name' => 'qita',
                    'is_delivery_type' => true,
                    'is_get_delivery_price' => false,
                    'order' => 3,
                    'has_children' => 0,
                    'country' => LocateConstant::COUNTRY_CN
                ],
            ]
        );
    }
}
