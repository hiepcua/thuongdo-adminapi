<?php


namespace App\Http\Resources\Traits;


trait HasPaginate
{
    public function getPaginateInfo(): array
    {
        $resource = $this->resource;
        return [
            'total' => $resource->total(),
            'per_page' => $resource->perPage(),
            'current_page' => $resource->currentPage(),
            'last_page' => $resource->lastPage()
        ];
    }
}