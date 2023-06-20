<?php


namespace App\Services;


use App\Http\Resources\OnlyIdNameResource;

class LabelService extends BaseService
{
    protected string $_resource = OnlyIdNameResource::class;
}