<?php

namespace App\Models;

use App\Constants\TimeConstant;
use App\Helpers\TimeHelper;
use App\Models\Traits\OrderMorphRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportOrderVN extends BaseModel
{
    use HasFactory, OrderMorphRelation;

    protected $table = 'report_orders_vn';

    protected $appends = ['shipping_cost', 'order_code', 'date_ordered', 'order_amount'];

    protected $casts = [
        'deposit_cost' => 'float',
        'order_cost' => 'float',
        'order_fee' => 'float',
        'inspection_cost' => 'float',
        'insurance_cost' => 'float',
        'woodworking_cost' => 'float',
        'storage_cost' => 'float',
        'shock_proof_cost' => 'float',
        'international_shipping_cost' => 'float',
        'china_shipping_cost' => 'float',
        'delivery_cost' => 'float',
    ];

    public function getShippingCostAttribute(): float
    {
        return $this->inspection_cost + $this->insurance_cost +
            $this->woodworking_cost + $this->storage_cost + $this->shock_proof_cost +
            $this->international_shipping_cost + $this->china_shipping_cost + $this->delivery_cost;
    }

    public function getOrderAmountAttribute(): float
    {
        return $this->order_cost + $this->order_fee;
    }

    public function getOrderCodeAttribute()
    {
        return optional($this->order)->code;
    }

    public function getDateOrderedAttribute(): ?string
    {
        return TimeHelper::format(
            optional($this->order)->date_ordered,
            TimeConstant::DATE_VI
        );
    }
}
