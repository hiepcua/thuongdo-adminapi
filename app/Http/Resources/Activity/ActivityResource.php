<?php

namespace App\Http\Resources\Activity;

use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $subject = (new $this->subject_type);
        $causer = $this->causer;
        return [
            'time' => TimeHelper::format($this->created_at),
            'label' => ['name' => $subject->getTableFriendly(), 'color' => $subject->getColorLog()],
            'content' => sprintf($this->content, optional($causer)->name)
        ];
    }
}
