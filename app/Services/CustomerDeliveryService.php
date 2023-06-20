<?php


namespace App\Services;


use App\Constants\TimeConstant;
use App\Http\Resources\CustomerDelivery\CustomerDeliveryResource;
use App\Models\CustomerDelivery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CustomerDeliveryService extends BaseService
{
    protected string $_resource = CustomerDeliveryResource::class;
    /**
     * @return Builder|Model|object|null
     */
    public function getDeliveryRandomByCustomer()
    {
        return CustomerDelivery::query()->inRandomOrder()->firstOrFail();
    }

    /**
     * storeMultiRecord
     * @param $array
     * @return void
     */
    public function storeMultiRecord($id, $array)
    {
        $data = [];
        foreach ($array as $item) {
            $item['id'] = getUuid();
            $item['customer_id'] = $id;
            $item['created_at'] = date(TimeConstant::DATETIME);
            $data[] = $item;
        }
        CustomerDelivery::query()->insert($data);
    }

    public function getListByCustomerId(string $customerId)
    {
        return CustomerDelivery::query()->where('customer_id', $customerId)->get();
    }
}
