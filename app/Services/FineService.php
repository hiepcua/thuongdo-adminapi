<?php


namespace App\Services;


use App\Http\Resources\FineResource;
use App\Models\Consignment;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class FineService extends BaseService
{
    protected string $_resource = FineResource::class;

    public function store(array $data)
    {
        if(isset($data['order_id'])) {
            $data['source_type'] = Order::class;
            $data['source_id'] = $data['order_id'];
        }
        $data['cause_id'] = getCurrentUser()->id;
        return parent::store($data);
    }

}