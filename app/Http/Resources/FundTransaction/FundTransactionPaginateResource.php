<?php

namespace App\Http\Resources\FundTransaction;

use App\Http\Resources\Traits\HasPaginate;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class FundTransactionPaginateResource extends ResourceCollection
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
                    return new FundTransactionResource($item);
                }
            ),
            'pagination' => $this->getPaginateInfo()
        ];
    }
}
