<?php

namespace App\Http\Resources\FundTypePay;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FundTypePayListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->transform(
            function ($item) {
                return new FundTypePayResource($item);
            }
        );
    }
}
