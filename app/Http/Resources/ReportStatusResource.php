<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportStatusResource extends JsonResource
{
    public ?int $_quantity;
    private array $_statuses;
    private array $_colors;

    public function __construct($resource, array $statuses, array $colors, ?int $quantity = null)
    {
        $this->_quantity = $quantity;
        $this->_statuses = $statuses;
        $this->_colors = $colors;
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
        $status = $this->resource;
        $data = [
            'value' => $status,
            'name' => $this->_statuses[$status],
            'color' => $this->_colors[$status]
        ];
        if (!is_null($this->_quantity)) {
            $data['quantity'] = $this->_quantity;
        }
        return $data;
    }
}
