<?php

namespace App\Http\Resources\FundTypePay;

use Illuminate\Http\Resources\Json\JsonResource;

class FundTypePayResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'type'       => $this->type,
            'type_txt'   => $this->getType(),
            'can_edit'   => $this->code == 0 ? true : false,
            'status'     => $this->status ? true : false,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null
        ];
    }
}
