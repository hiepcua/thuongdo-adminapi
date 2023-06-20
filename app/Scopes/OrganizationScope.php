<?php


namespace App\Scopes;


use App\Constants\OrganizationConstant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $organization = request()->header('x-organization');
        $isSuperAdmin = $organization === OrganizationConstant::ADMIN_ORGANIZATION;
        if (!$organization) {
            return;
        }
        if (!$isSuperAdmin) {
            $condition = [
                $model->getTable() .'.organization_id' => $organization
            ];
            request()->request->add($condition);
            $isColExist = Schema::hasColumn($model->getTable(),'organization_id');
            if(!$isColExist) return;
            $builder->where($condition);
        }
        unset($condition);
    }
}