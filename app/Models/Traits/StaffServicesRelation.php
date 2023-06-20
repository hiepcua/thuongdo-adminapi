<?php


namespace App\Models\Traits;


use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait StaffServicesRelation
{
    public function staffOrder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_order_id', 'id');
    }

    public function staffSale(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_sale_id', 'id');
    }

    public function staffQuotation(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_quotation_id', 'id');
    }

    public function staffCare(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_care_id', 'id');
    }

    public function staffCounselor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_counselor_id', 'id');
    }

    public function staffComplain(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_complain_id', 'id');
    }
}