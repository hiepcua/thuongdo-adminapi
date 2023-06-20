<?php

namespace App\Http\Resources\Module;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ModuleResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $permissions = $this->permissions;
        return [
            'id'=> $this->id,
            'name'=> $this->name,
            'permissions'=> $permissions
        ];
    }
}