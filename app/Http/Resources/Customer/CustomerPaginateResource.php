<?php

namespace App\Http\Resources\Customer;

use App\Http\Resources\Traits\HasPaginate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class CustomerPaginateResource extends ResourceCollection
{
    use HasPaginate;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'items' => $this->collection->transform(
                function ($item) {
                    return new CustomerResource($item);
                }
            ),
            'pagination' => $this->getPaginateInfo()
        ];
    }
}
