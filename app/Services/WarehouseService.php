<?php


namespace App\Services;


use App\Constants\LocateConstant;
use App\Http\Resources\Warehouse\WarehouseResource;
use App\Models\Warehouse;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WarehouseService extends BaseService
{
    protected string $_resource = WarehouseResource::class;
    /**
     * @param  string  $country
     * @return Builder|Model|object|null
     */
    public function getWarehouseRandomByCountry(string $country)
    {
        return Warehouse::query()->withoutGlobalScope(OrganizationScope::class)->where(
            ['country' => $country]
        )->firstOrFail();
    }

    /**
     * @param  string  $id
     * @return string
     */
    public function getProvinceById(string $id): string
    {
        $province = array_search(
            Warehouse::query()->withoutGlobalScope(OrganizationScope::class)->find($id)->province->name,
            LocateConstant::HANOI_HCM_HP
        );
        $province = $province === false ? LocateConstant::HANOI : $province;
        return $province;
    }

    /**
     * @param  string  $country
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getWarehousesCountry(string $country)
    {
        return Warehouse::query()->where(['country' => $country])->select('id', 'name', 'province_id', 'address')->get();
    }
}