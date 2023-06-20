<?php

namespace App\Http\Resources\Fund;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FundListResource extends ResourceCollection
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
                return new FundResource($item);
            }
        );
    }
}
