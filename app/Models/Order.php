<?php

namespace App\Models;

use App\Helpers\AccountingHelper;
use App\Models\Traits\CustomerDeliveryRelation;
use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\CodeFilter;
use App\Models\Traits\Filters\CreatedAtFilter;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\Filters\WarehouseFilter;
use App\Models\Traits\StaffServicesRelation;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use App\Services\ConfigService;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 * @package App\Models
 *
 * @property HasMany $details
 * @property float $exchange_rate
 * @property float $total_order
 * @property float $order_fee
 * @property float $order_cost
 * @property float $total_amount
 * @property float $woodworking_cost
 * @property float $discount_cost
 * @property float $inspection_cost
 * @property float $china_shipping_cost
 * @property float $international_shipping_cost
 * @property float $deposit_cost
 * @property string $status
 * @property string $reason_cancel
 * @property string $code
 * @property string $ecommerce
 * @property string $customer_id
 * @property string $customer_delivery_id
 * @property string $staff_care_id
 * @property string $staff_quotation_id
 * @property string $staff_order_id
 * @property string $id
 * @property boolean $is_inspection
 * @property boolean $is_woodworking
 * @property boolean $is_shock_proof
 * @property int $packages_number
 */
class Order extends BaseModel
{
    use HasFactory, Filterable, StatusFilter, SoftDeletes, StaffServicesRelation, CustomerDeliveryRelation, CustomerRelation, WarehouseFilter, CodeFilter, CreatedAtFilter, WarehouseRelation;

    public string $_colorLog = '#00BFC4';

    protected string $_tableNameFriendly = 'Đơn hàng';


    protected $appends = [
        'total_amount',
        'order_cost_cny',
        'debt_cost'
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'order_cost' => 'float',
        'total_amount' => 'float',
        'inspection_cost' => 'float',
        'woodworking_cost' => 'float',
        'discount_cost' => 'float',
        'deposit_cost' => 'float',
        'china_shipping_cost' => 'float',
        'international_shipping_cost' => 'float',
        'order_fee' => 'float',
        'delivery_cost' => 'float',
        'date_ordered' => 'datetime'
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function complains(): HasMany
    {
        return $this->hasMany(Complain::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }

    public function notePublic(): HasMany
    {
        return $this->hasMany(OrderNote::class)->where('is_public', true);
    }

    public function notePrivate(): HasMany
    {
        return $this->hasMany(OrderNote::class)->where('is_public', false);
    }


    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->order_cost + $this->order_fee + $this->woodworking_cost - $this->discount_cost + $this->inspection_cost + $this->china_shipping_cost + $this->international_shipping_cost;
    }

    public function getOrderCostCnyAttribute(): float
    {
        return AccountingHelper::getCosts($this->order_cost / ($this->exchange_rate  ?? (new ConfigService())->getExchangeRate()));
    }

    public function getDebtCostAttribute(): float
    {
        return $this->total_amount - $this->deposit_cost;
    }

    public function scopeCustomerName($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('name', request()->query('customer_name'));
        });
    }

    public function scopeCustomerLevel($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('level', request()->query('customer_level'));
        });
    }

    public function scopeCustomerPhone($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('phone_number', request()->query('customer_phone'));
        });
    }

    public function scopeCustomerVia($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('via', request()->query('customer_via'));
        });
    }

    public function scopeCustomerBusinessType($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('business_type', request()->query('customer_business_type'));
        });
    }

    public function scopeStaffOrderId($query)
    {
        return $query->where('staff_order_id', request()->query('staff_order_id'));
    }

    public function scopeStaffQuotationId($query)
    {
        return $query->where('staff_quotation_id', request()->query('staff_quotation_id'));
    }

    public function scopeStaffCareId($query)
    {
        return $query->where('staff_care_id', request()->query('staff_care_id'));
    }

    public function scopeIsWebsite($query)
    {
        return $query->where('is_website', (int)request()->query('is_website'));
    }

    public function scopeIsTax($query)
    {
        return $query->where('is_tax', request()->query('is_tax'));
    }

    public function scopeIsPurchase($query)
    {
        return $query->where('deposit_cost', (bool)request()->query('is_purchase') ? '>' : '=', 0 );
    }

    public function scopeEcommerce($query)
    {
        return $query->where('ecommerce', request()->query('ecommerce'));
    }

    public function scopeCodePo($query)
    {
        return $query->where('code_po', request()->query('code_po'));
    }

    public function scopeDateDone($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date_done'), 'date_done');
    }

    public function scopeDatePurchased($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date_purchased'), 'date_purchased');
    }

    public function scopeDateOrdered($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date_ordered'), 'date_ordered');
    }

    public function scopeDateQuotation($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date_quotation'), 'date_quotation');
    }

    public function scopeCreatedAtSort($query)
    {
        return $query->orderBy('created_at', request()->query('created_at_sort'));
    }

    public function scopeOrderCostSort($query)
    {
        return $query->orderBy('order_cost', request()->query('order_cost_sort'));
    }
}
