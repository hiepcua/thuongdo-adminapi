<?php


namespace App\Services;


use App\Models\Supplier;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SupplierService extends BaseService
{
    /**
     * @param string $organizationId
     * @return Builder|Model|object|null
     */


    public function getSupplierRandomByOrganization(string $organizationId)
    {
        return Supplier::query()->withoutGlobalScope(OrganizationScope::class)->where(
            'organization_id',
            $organizationId
        )->inRandomOrder()->first();
    }
}
