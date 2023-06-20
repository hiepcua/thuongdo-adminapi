<?php

namespace App\Http\Resources\Note;

use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => TimeHelper::format($this->created_at),
            'user' => optional($this->staff)->only('id', 'name')
        ];
    }
}
