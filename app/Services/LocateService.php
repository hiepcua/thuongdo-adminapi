<?php


namespace App\Services;


use App\Models\Province;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LocateService extends BaseService
{
    /**
     * @param  string  $country
     * @return Builder|Model|object|null
     */
    public function getProvinceByCountry(string $country)
    {
        return Province::query()->where('country', $country)->first();
    }

    public function getProvincesByCountry(string $country)
    {
        return Province::query()
            ->where('country', $country)
            ->orderBy('name')
            ->select('id', 'name')->get();
    }
}
