<?php


namespace App\Services;


use App\Models\Category;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryService extends BaseService
{
    /**
     * @param  string  $organizationId
     * @return Builder|Model|object|null
     */
    public function getCategoryRandomByOrganization(string $organizationId)
    {
        return Category::query()->withoutGlobalScope(OrganizationScope::class)->where(
            'organization_id',
            $organizationId
        )->inRandomOrder()->first();
    }
}