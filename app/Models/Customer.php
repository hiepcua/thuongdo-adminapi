<?php

namespace App\Models;

use App\Constants\TimeConstant;
use App\Models\Traits\Filters\CreatedAtFilter;
use App\Models\Traits\Filters\WarehouseFilter;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasOrganization;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class Customer
 * @package App\Models
 *
 * @property string $id
 * @property string $organization_id
 * @property string $warehouse_id
 * @property string $code
 * // * @method Builder search
 */
class Customer extends BaseModel
{
    use HasFactory, Filterable, SoftDeletes, HasSortDescByCreated, HasOrganization, WarehouseFilter, WarehouseRelation, CreatedAtFilter;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected string $_tableNameFriendly = 'Khách hàng';

    public function label(): BelongsTo
    {
        return $this->belongsTo(Label::class);
    }

    public function report(): HasOne
    {
        return $this->HasOne(ReportCustomer::class);
    }
    public function delivery(): HasMany
    {
        return $this->HasMany(CustomerDelivery::class);
    }
    public function offer(): HasOne
    {
        return $this->HasOne(CustomerOffer::class);
    }

    public function scopeCode($query)
    {
        return $query->where('code', request()->input('code'));
    }

    public function scopeEmail($query)
    {
        return $query->where('email', request()->input('email'));
    }

    public function scopePhoneNumber($query)
    {
        return $query->where('phone_number', request()->input('phone_number'));
    }

    public function scopeProvinceId($query)
    {
        return $query->where('province_id', request()->input('province_id'));
    }

    public function scopeLevel($query)
    {
        return $query->where('level', request()->input('level'));
    }

    public function scopeLabelId($query)
    {
        return $query->where('label_id', request()->input('label_id'));
    }

    public function scopeBusinessType($query)
    {
        return $query->where('business_type', request()->input('business_type'));
    }

    public function scopeStaffCareId($query)
    {
        return $query->where('staff_care_id', request()->input('staff_care_id'));
    }

    public function scopeStaffCounselorId($query)
    {
        return $query->where('staff_counselor_id', request()->input('staff_counselor_id'));
    }

    public function scopeStaffOrderId($query)
    {
        return $query->where('staff_order_id', request()->input('staff_order_id'));
    }

    public function scopeVia($query)
    {
        return $query->where('via', request()->input('via'));
    }

    public function scopeService($query)
    {
        return $query->where('service', request()->input('service'));
    }

    public function scopeCustomerReasonInactiveId($query)
    {
        return $query->where('customer_reason_inactive_id', request()->input('customer_reason_inactive_id'));
    }

    public function scopeLastOrder($query)
    {
        $end = Carbon::now();
        $start = Carbon::now()->subDays((int)request()->input('last_order'));
        return (new FilterService())->rangeDateFilter(
            $query,
            $start->format(TimeConstant::DATE).','.$end->format(
                TimeConstant::DATE
            ),
            'last_order_at'
        );
    }

    public function staffOrder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_order_id', 'id');
    }

    public function staffSale(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_sale_id', 'id');
    }

    public function staffCare(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_care_id', 'id');
    }

    public function staffCounselor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_counselor_id', 'id');
    }

}
