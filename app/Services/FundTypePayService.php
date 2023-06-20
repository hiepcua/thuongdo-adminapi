<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Models\FundTypePay;
use App\Http\Resources\FundTypePay\FundTypePayResource;
use App\Http\Resources\FundTypePay\FundTypePayListResource;
use App\Http\Resources\FundTypePay\FundTypePayPaginateResource;

class FundTypePayService extends BaseService
{
    protected string $_paginateResource = FundTypePayPaginateResource::class;
    protected string $_listResource = FundTypePayListResource::class;
    protected string $_resource = FundTypePayResource::class;

    public function store(array $data)
    {
        $this->throwModel();
        $userOnline = \Auth::user();

        $data['organization_id'] = $userOnline->organization_id;
        $data['status'] = 1;

        return $this->_model->newQuery()->create($data);
    }
}
