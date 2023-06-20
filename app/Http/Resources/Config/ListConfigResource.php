<?php

namespace App\Http\Resources\Config;

use App\Constants\ConfigConstant;
use App\Http\Resources\Resource;
use App\Services\CustomerService;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ListConfigResource extends ResourceCollection
{
    private ?string $_resource;

    public function __construct($resource, ?string $class = Resource::class)
    {
        $this->_resource = $class;
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
        $this->collection->where('is_publish', 1)->transform(
            function ($item) use (&$data) {
                $value = json_decode($item->value);
                $data[$item->key] = $value;
            }
        );
        ksort($data);
        return $data;
    }
}
