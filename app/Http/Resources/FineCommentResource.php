<?php

namespace App\Http\Resources;

use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class FineCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user' => optional($this->staff)->only('id', 'name', 'avatar'),
            'content' => $this->content,
            'time' => TimeHelper::format($this->created_at)
        ];
    }
}
