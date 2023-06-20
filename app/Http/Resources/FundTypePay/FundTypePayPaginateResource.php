<?php

namespace App\Http\Resources\FundTypePay;

use App\Http\Resources\Traits\HasPaginate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class FundTypePayPaginateResource extends ResourceCollection
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
                    return new FundTypePayResource($item);
                }
            ),
            'pagination' => $this->getPaginateInfo()
        ];
    }
}
