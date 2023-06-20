<?php

namespace App\Models;

use App\Models\Traits\DistrictRelation;
use App\Models\Traits\ProvinceRelation;
use App\Models\Traits\WardRelation;
use App\Scopes\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CustomerDelivery
 * @package App\Models
 *
 * @property string $id
 * @property string $province_id
 * @property string $ward_id
 * @property string $country
 */

class CustomerDelivery extends BaseModel
{
    use HasFactory, Filterable, ProvinceRelation, DistrictRelation, WardRelation;

    protected $appends = [
        'custom_name',
        'custom_name_Second',
        'address_only',
    ];


    public function scopeCustomerId($query)
    {
        return $query->where('customer_id', request()->input('customer_id'));
    }

    /**
     * @return string
     */
    public function getCustomNameAttribute(): string
    {
        return ucwords(
            mb_strtolower(
                $this->receiver.' - '.$this->phone_number.' - '.$this->address.' - '.optional(
                    $this->ward
                )->name.' - '.optional($this->district)->name.' - '.optional($this->province)->name
            )
        );
    }

    /**
     * @return string
     */
    public function getCustomNameSecondAttribute(): string
    {
        return ucwords(
            mb_strtolower(
                $this->address.' - '.optional(
                    $this->ward
                )->name.' - '.optional($this->district)->name.' - '.optional($this->province)->name
            )
        );
    }

    /**
     * @return string
     */
    public function getAddressOnlyAttribute(): string
    {
        return ucwords(
            mb_strtolower(
                $this->address.' - '.optional(
                    $this->ward
                )->name.' - '.optional($this->district)->name.' - '.optional($this->province)->name
            )
        );
    }
}
