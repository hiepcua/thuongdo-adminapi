<?php

namespace App\Http\Resources\Note;

use App\Helpers\TimeHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderNoteResource extends JsonResource
{

    private string $_column;

    public function __construct($resource, string $column)
    {
        $this->_column = $column;
        parent::__construct($resource);
    }

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
            'subject' => optional($this->subject)->only('id', 'name', 'avatar'),
            $this->_column => $this->{$this->_column},
            'content' => $this->content,
            'time' => TimeHelper::format($this->created_at)
        ];
    }
}
