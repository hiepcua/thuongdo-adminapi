<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OnlyValueKeyResource extends JsonResource
{
    private ?array $_data;
    public function __construct($resource, ?array $data = null)
    {
        $this->_data = $data;
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
        $data = [];
        foreach ($this->resource as $key => $value) {
            $tmp = [
                'name' => $value,
                'value' => $key
            ];
            if($this->_data) {
                $tmp['data'] = $this->_data;
            }
            $data[] = $tmp;
        }
        return $data;
    }
}
