<?php


namespace App\Services;


use App\Constants\ConfigConstant;
use App\Helpers\AccountingHelper;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class AccountingService extends BaseService
{
    private ConfigService $_configService;

    public function __construct()
    {
        parent::__construct();
        $this->_configService = new ConfigService();
    }

    /**
     * @param  array  $data
     */
    public function calculatorServiceCost(array &$data)
    {
        $data['exchange_rate'] = (new ConfigService())->getExchangeRate();
        $data['total_order'] = $this->getTotalOrderInProducts($data['products'], $data['exchange_rate']);
        $data['inspection_cost'] = $data['is_inspection'] ? $this->getInspectionCost(array_sum($data['products'])) : 0;
        $data['discount_cost'] = $this->getCustomerLevelCost($data['total_order'], optional(Auth::user())->level ?? 1);
        $data['order_fee'] = $this->getOrderFee($data['total_order']);
    }

    /**
     * Tính phí dịch vụ kiểm hàng
     * @param  int  $productsNumber
     * @return float
     */
    public function getInspectionCost(int $productsNumber): float
    {
        return (float)$this->_configService->getResultFromValueByBetweenMinMax(
                ConfigConstant::SERVICE_INSPECTION,
                $productsNumber
            ) * $productsNumber;
    }

    /**
     * @param  float  $weight
     * @param  float  $volume
     * @param  bool  $isWeightGreaterThanVolume
     * @return float
     */
    public function getWoodworkingCost(float $weight, float $volume, &$isWeightGreaterThanVolume = true): float
    {
        if($weight == 0 && $volume == 0) return 0;
        $weight = roundHalfUp($weight);
        $weightJson = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_WOODWORKING_WEIGHT));
        $cost = $this->getCostByFirstSubsequent($weightJson, $weight);
        $volumeJson = $this->_configService->getResultFromValueByBetweenMinMax(
            ConfigConstant::SERVICE_WOODWORKING_VOLUME,
            $volume
        );
        $volumeCost = $this->getCostByFirstSubsequent($volumeJson, $volume);
        return ($isWeightGreaterThanVolume = $cost > $volumeCost) ? $cost : $volumeCost;
    }

    /**
     * Phí vận chuyển quốc tế (Trung Quốc)
     * @param  string  $province
     * @param  float  $weight
     * @param  float  $volume
     * @param  bool|null  $isWeight
     * @return float
     */
    public function getInternationShippingCost(string $province, float $weight, float $volume, ?bool &$isWeight = true): float
    {
        $province = str_replace('-', '', Str::slug(optional(Province::query()->find($province))->name));

        $weight = roundHalfUp($weight);

        $weightCost = $this->getCostByProvince(ConfigConstant::SERVICE_PACKAGE_SHIPPING_WEIGHT, $weight, $province);
        $volumeCost = $this->getCostByProvince(ConfigConstant::SERVICE_PACKAGE_SHIPPING_VOLUME, $volume, $province);
        $isWeight = $weightCost >= $volumeCost;
        return AccountingHelper::getCosts($isWeight ? $weightCost : $volumeCost);
    }

    /**
     * @param  string  $key
     * @param  float  $value
     * @param  string  $province
     * @return float
     */
    private function getCostByProvince(string $key, float $value, string $province): float
    {
        $json = $this->_configService->getResultFromValueByBetweenMinMax($key, $value);
        return $json ? (($json->{$province} ?? 0) * $value) : 0;
    }

    /**
     * @param  float  $totalOrder
     * @param  float|null  $orderPercent
     * @return float
     */
    public function getOrderFee(float $totalOrder, ?float &$orderPercent = 0): float
    {
        $percent = $this->_configService->getResultFromValueByBetweenMinMax(
            ConfigConstant::SERVICE_ORDER_FEE,
            $totalOrder
        );
        $default = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_ORDER_FEE), true)[0]['default'];
        // Percent mà > 100 thì nó sẽ là giá trị tối thiểu đặt hàng 9000/đơn
        $orderPercent = $percent > 100 ? 3 : $percent;
        return AccountingHelper::getCosts($percent > 100  ? $default : max(($totalOrder * ($percent / 100)), $default));
    }


    /**
     * @param  float  $totalOrder
     * @param  int  $level
     * @return float
     */
    public function getCustomerLevelCost(float $totalOrder, int $level): float
    {
        return AccountingHelper::getCosts(
            $totalOrder * $this->getPercent($this->_configService->getResultFromValueByLevel($level)->order)
        );
    }

    /**
     * @param  float  $orderCost
     * @return float
     */
    public function getInsuranceCost(float $orderCost): float
    {
        return AccountingHelper::getCosts(
            $orderCost * $this->getPercent(
                (float)$this->_configService->getValueByKey(ConfigConstant::SERVICE_INSURANCE)
            )
        );
    }

    /**
     * @param  float|null  $number
     * @return float|int
     */
    public function getPercent(?float $number): float
    {
        return ($number ?? 0) / 100;
    }

    /**
     * @param $item
     * @param  float  $value
     * @return float
     */
    private function getCostByFirstSubsequent($item, float $value): float
    {
        if (!$item) {
            return 0;
        }
        if ($value > 1) {
            return ((float)$item->first + (float)$item->subsequent * ($value - 1));
        }
        return (float)$item->first * $value;
    }

    /**
     * @param  array  $products
     * @param  float  $exchangeRate
     * @return float
     */
    private function getTotalOrderInProducts(array $products, float $exchangeRate): float
    {
        $results = 0;
        foreach ($products as $product) {
            $results += $product['quantity'] * $product['unit_price_cny'];
        }
        return AccountingHelper::getCosts($results * $exchangeRate);
    }

    /**
     * @param  float  $orderCost
     * @return float
     */
    public function getDiscountCost(float $orderCost): float
    {
        return $this->getCustomerLevelCost($orderCost, optional(Auth::user())->level ?? 1);
    }

    /**
     * @param  float  $weight
     * @return float
     */
    public function getShockCost(float $weight): float
    {
        $weight = roundHalfUp($weight);
        $shockJson = json_decode($this->_configService->getValueByKey(ConfigConstant::SERVICE_SHOCK_PROOF));
        return $weight == 0 ? 0 : $this->getCostByFirstSubsequent($shockJson, $weight);
    }
}