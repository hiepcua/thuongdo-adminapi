<?php


namespace App\Services\Carriers;

use App\Constants\CarrierConstant;
use App\Helpers\ConvertHelper;
use App\Models\CustomerDelivery;
use App\Models\Warehouse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GHNService implements Carrier
{

    public function getPrice(array $array): float
    {
        $url = config('carrier.ghn').CarrierConstant::ENDPOINT_GHN_PRICE;
        $body = $this->getParams($array);
        try {
            $response = Http::withHeaders($this->getHeaders())->get(
                $url."?".http_build_query($body)
            )->throw()->json();

            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, $response);

            if ($response['code'] !== 200) {
                return self::FAIL;
            }
            return $response['data']['service_fee'];
        } catch (RequestException $e) {
            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, [$e->getMessage()]);
            return self::FAIL;
        }
    }

    public function getHeaders(): array
    {
        return (array)(new CarrierService())->getConfigByCarrier(CarrierConstant::GHN);
    }

    public function getParams(array $array): ?array
    {
        [$packages, $warehouse, $delivery] = (new CarrierService())->getVariables(
            $array['warehouse_id'],
            $array['packages'],
            $array['customer_delivery_id']
        );

        /** @var Warehouse $warehouse */
        /** @var CustomerDelivery $delivery */
        /** @var Collection $packages */

        if (!$warehouse->province || !$warehouse->district || !$delivery->province || !$delivery->district) {
            return null;
        }

        return [
            "from_district_id" => $warehouse->district->ghn_id,
            "service_type_id" => 2,
            "to_district_id" => $delivery->district->ghn_id,
            "to_ward_code" => $delivery->ward->code,
            "height" => ConvertHelper::floatToInt((clone $packages)->max('height')),
            "length" => ConvertHelper::floatToInt((clone $packages)->max('length')),
            "width" => ConvertHelper::floatToInt((clone $packages)->max('width')),
            "weight" => ConvertHelper::floatToInt((clone $packages)->sum('weight')),
            "insurance_value" => ConvertHelper::floatToInt((clone $packages)->sum('order_cost')),
            "coupon" => null
        ];
    }
}