<?php


namespace App\Services\Carriers;

use App\Constants\CarrierConstant;
use App\Constants\ConfigConstant;
use App\Models\CarrierActivity;
use App\Models\CustomerDelivery;
use App\Models\OrderPackage;
use App\Models\Warehouse;
use App\Services\ConfigService;
use App\Services\Service;
use Illuminate\Support\Facades\Auth;

class CarrierService implements Service
{
    public function getPrice(array $data): float
    {
        switch ($data['carrier']) {
            case CarrierConstant::GHTK:
                return (new GHTKService())->getPrice($data);
            case CarrierConstant::GHN:
                return (new GHNService())->getPrice($data);
            case CarrierConstant::VIETTEL:
                return (new ViettelService())->getPrice($data);
            default:
                return -1;
        }
    }

    /**
     * @param  string  $carrier
     * @return mixed
     */
    public function getConfigByCarrier(string $carrier)
    {
        return json_decode((new ConfigService())->getValueByKey(ConfigConstant::CARRIERS))->{$carrier};
    }

    /**
     * @param  string  $warehouseId
     * @param  array  $packages
     * @param  string  $customerDeliveryId
     * @return array
     */
    public function getVariables(string $warehouseId, array $packages, string $customerDeliveryId): array
    {
        $packages = OrderPackage::query()->findMany($packages);

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::query()->findOrFail($warehouseId);

        /** @var CustomerDelivery $delivery */
        $delivery = CustomerDelivery::query()->findOrFail($customerDeliveryId);

        return [$packages, $warehouse, $delivery];
    }

    /**
     * @param  string  $deliveryId
     * @param  array  $body
     * @param  array  $response
     */
    public function setActivity(string $deliveryId, array $body, array $response): void
    {
        CarrierActivity::query()->create(
            [
                'customer_delivery_id' => $deliveryId,
                'body' => json_encode($body),
                'response' => json_encode($response)
            ]
        );
    }
}