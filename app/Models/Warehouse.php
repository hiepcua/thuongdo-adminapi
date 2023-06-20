<?php

namespace App\Models;

use App\Models\Traits\DistrictRelation;
use App\Models\Traits\ProvinceRelation;
use App\Scopes\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Warehouse extends BaseModel
{
    use HasFactory, HasOrganization, ProvinceRelation, DistrictRelation;

    protected $appends = [
        'custom_name',
        'custom_name_second',
    ];

    /**
     * @return string
     */
    public function getCustomNameAttribute(): string
    {
        return optional($this->province)->name . ' - ' . $this->address;
    }

    /**
     * @return string
     */
    public function getCustomNameSecondAttribute(): string
    {
        return  $this->address . ', ' .optional($this->province)->name;
    }

}
