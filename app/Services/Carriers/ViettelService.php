<?php


namespace App\Services\Carriers;

use App\Constants\CarrierConstant;
use App\Models\CustomerDelivery;
use App\Models\Warehouse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ViettelService implements Carrier
{

    private string $_url;
    private string $_login;
    private string $_price;
    private string $_certificate;

    public function __construct()
    {
        $this->_url = config('carrier.viettel');
        $this->_login = $this->_url.CarrierConstant::ENDPOINT_VIETTEL_LOGIN;
        $this->_price = $this->_url.CarrierConstant::ENDPOINT_VIETTEL_PRICE;
        $this->_certificate = json_encode((new CarrierService())->getConfigByCarrier(CarrierConstant::VIETTEL));
    }

    public function getPrice(array $array): float
    {
        $body = $this->getParams($array);
        try {
            $response = Http::withHeaders($this->getHeaders())->withBody(
                json_encode($body),
                'application/json'
            )->post(
                $this->_price
            )->throw()->json();
            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, $response);
            if ($response['status'] !== 200) {
                return self::FAIL;
            }
            return $response['data']['MONEY_TOTAL_FEE'];
        } catch (RequestException $e) {
            (new CarrierService())->setActivity($array['customer_delivery_id'], $body, [$e->getMessage()]);
            return self::FAIL;
        }
    }

    public function getHeaders(): array
    {
        return [
            'token' => $this->getToken()
        ];
    }

    public function getParams(array $array): ?array
    {
        /** @var Warehouse $warehouse */
        /** @var CustomerDelivery $delivery */
        /** @var Collection $packages */
        [$packages, $warehouse, $delivery] = (new CarrierService())->getVariables(
            $array['warehouse_id'],
            $array['packages'],
            $array['customer_delivery_id']
        );
        return [
            "PRODUCT_WEIGHT" => (clone $packages)->sum('weight'),
            "PRODUCT_PRICE" => (clone $packages)->sum('order_cost'),
            "MONEY_COLLECTION" => 0,
            "ORDER_SERVICE_ADD" => "",
            "ORDER_SERVICE" => $array['delivery_type'] === 'fast' ? "VCN" : 'VCB',
            "SENDER_PROVINCE" => (string)$warehouse->province->viettel_id,
            "SENDER_DISTRICT" => (string)$warehouse->district->viettel_id,
            "RECEIVER_PROVINCE" => (string)$delivery->province->viettel_id,
            "RECEIVER_DISTRICT" => (string)$delivery->district->viettel_id,
            "PRODUCT_TYPE" => "HH",
            "NATIONAL_TYPE" => 1
        ];
    }

    /**
     * @return string|null
     */
    private function getToken(): ?string
    {
        try {
            $response = Http::withBody($this->_certificate, 'application/json')->post($this->_login)->throw()->json();
            return $response['data']['token'];
        } catch (RequestException $e) {
            return null;
        }
    }
}