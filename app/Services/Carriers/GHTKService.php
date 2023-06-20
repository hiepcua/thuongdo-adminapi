<?php


namespace App\Services\Carriers;

use App\Constants\CarrierConstant;
use App\Helpers\ConvertHelper;
use App\Models\CustomerDelivery;
use App\Models\Warehouse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GHTKService implements Carrier
{
    /**
     * @param  array  $array
     * @return float
     */
    public function getPrice(array $array): float
    {
        $url = config('carrier.ghtk').CarrierConstant::ENDPOINT_GHTK_PRICE;
        $body = $this->getParams($array);
        try {
            $response = Http::withHeaders($this->getHeaders())->get(
                $url."?".http_build_query($body)
            )->throw()->json();

            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, $response);

            if (!$response['success']) {
                return self::FAIL;
            }
            return $response['fee']['fee'];
        } catch (RequestException $e) {
            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, [$e->getMessage()]);
            return self::FAIL;
        }
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return (array)(new CarrierService())->getConfigByCarrier(CarrierConstant::GHTK);
    }

    /**
     * @param  array  $array
     * @return array|null
     */
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
        $weight = $packages->sum('weight');

        if (!$warehouse->province || !$warehouse->district || !$delivery->province || !$delivery->district) {
            return null;
        }

        return [
            "pick_province" => $warehouse->province->name,
            "pick_district" => $warehouse->district->name,
            "province" => $delivery->province->name,
            "district" => $delivery->district->name,
            "address" => $delivery->address,
            "weight" => ConvertHelper::kilogramToGram($weight),
            "transport" => 'road',
            "deliver_option" => $array['delivery_type'] === 'fast' ? 'xteam' : 'none'
        ];
    }
}