<?php

namespace App\Http\Resources\Package;

use App\Http\Resources\ListResource;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resource->map(
            function ($item, $key) {
                return [
                    'suppliers' => Supplier::query()->find($key)->only('id', 'name'),
                    'products' => new ListResource($item,  ProductItemResource::class),
                ];
            }
        )->values();
    }
}
