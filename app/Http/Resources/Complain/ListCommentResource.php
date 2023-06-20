<?php

namespace App\Http\Resources\Complain;

use App\Constants\ComplainConstant;
use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\ListResource;
use App\Http\Resources\Resource;

class ListCommentResource extends ListResource
{
    private string $_type;

    public function __construct($resource, string $type, ?string $class = Resource::class)
    {
        parent::__construct($resource, $class);
        $this->_type = $type;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $sender = optional($this->first())->cause;
        $data['items'] = parent::toArray($this->resource);
        $data['quantity'] = $this->where('is_seen', false)->count();
        $data['sender'] = [
            'name' => ComplainConstant::NOTE_TYPES[$this->_type],
            'prefix' => 'Nhóm'
        ];
        $data['time'] = TimeHelper::format(optional($this->first())->created_at, TimeConstant::DATETIME_BY_DAY_HI);

        return $data;
    }
}
