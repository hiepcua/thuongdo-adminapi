<?php

namespace App\Models;

use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\CreatedAtFilter;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\OrderRelation;
use App\Models\Traits\StaffServicesRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortDescByCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complain extends BaseModel
{
    use HasFactory, OrderRelation, CustomerRelation, Filterable, StatusFilter, CreatedAtFilter, StaffServicesRelation, HasSortDescByCreated;

    protected string $_tableNameFriendly = 'Khiếu nại';

    public function orderingStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordering_staff_id', 'id');
    }

    public function handlingStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handling_staff_id', 'id');
    }

    public function orderDetails(): BelongsToMany
    {
        return $this->belongsToMany(OrderDetail::class, 'complain_details')->withPivot(['note']);
    }

    public function complainType(): BelongsTo
    {
        return $this->belongsTo(ComplainType::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ComplainImage::class);
    }

    public function scopeOrderCode($query)
    {
        return $query->whereHas(
            'order',
            function ($q) {
                return $q->where('code', request()->query('order_code'));
            }
        );
    }

    public function scopeCustomerName($query)
    {
        return $query->whereHas(
            'customer',
            function ($q) {
                return $q->where('name', 'ilike', '%'.request()->query('customer_name').'%');
            }
        );
    }

    public function scopeCustomerPhone($query)
    {
        return $query->whereHas(
            'customer',
            function ($q) {
                return $q->where('phone_number', request()->query('customer_phone'));
            }
        );
    }

    public function scopeStaffCareId($query)
    {
        return $query->where('staff_care_id', request()->query('staff_care_id'));
    }

    public function scopeStaffOrderId($query)
    {
        return $query->where('staff_order_id', request()->query('staff_order_id'));
    }

    public function scopeStaffComplainId($query)
    {
        return $query->where('staff_complain_id', request()->query('staff_complain_id'));
    }

    public function scopeSolutionId($query)
    {
        return $query->where('solution_id', request()->query('solution_id'));
    }

    public function scopeComplainTypeId($query)
    {
        return $query->where('complain_type_id', request()->query('complain_type_id'));
    }
}
