<?php

namespace App\Http\Resources\Department;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use JsonSerializable;

class DepartmentResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'organization_id' => $this->organization_id,
            'roles' => (new ListResource($this->roles, RoleResource::class)),
        ];
    }
}
