<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class WarehouseListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $data = [];
        $this->collection->each(function($item) use(&$data) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->custom_name
            ];
        });
        return $data;
    }
}
