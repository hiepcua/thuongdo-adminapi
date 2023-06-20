<?php

namespace App\Models;

use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Helpers\AccountingHelper;
use App\Models\Traits\CustomerDeliveryRelation;
use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\OrderMorphRelation;
use App\Models\Traits\StaffServicesRelation;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class OrderPackage
 * @package App\Models
 *
 * @property string $id
 * @property int $quantity
 * @property float $unit_price_cny
 * @property float $amount_cny
 * @property float $order_cost
 * @property float $insurance_cost
 * @property float $china_shipping_cost
 * @property float $international_shipping_cost
 * @property float $shock_proof_cost
 * @property float $inspection_cost
 * @property float $woodworking_cost
 * @property float $exchange_rate
 * @property float $storage_cost
 * @property float $delivery_cost
 * @property float $discount_cost
 * @property float $amount
 * @property float $weight
 * @property float $volume
 * @property float $height
 * @property float $length
 * @property float $width
 * @property bool $is_order
 * @property bool $is_insurance
 * @property bool $is_inspection
 * @property bool $is_woodworking
 * @property bool $is_shock_proof
 * @property string $order_code
 * @property string $customer_id
 * @property string $delivery_id
 * @property string $customer_delivery_id
 * @property CustomerDelivery $customerDelivery
 * @property Warehouse $warehouse
 */
class OrderPackage extends BaseModel
{
    use HasFactory, WarehouseRelation, CustomerDeliveryRelation, StaffServicesRelation, CustomerRelation, OrderMorphRelation, HasSortDescByCreated, Filterable, StatusFilter;

    protected $table = 'order_package';

    protected $_prefixRoute = 'package';

    protected $appends = ['amount', 'shipping_cost', 'order_kind_of', 'volume', 'is_order'];

    protected $casts = [
        'delivery_cost' => 'double',
        'international_shipping_cost' => 'double',
        'china_shipping_cost' => 'double',
        'inspection_cost' => 'double',
        'insurance_cost' => 'double',
        'storage_cost' => 'double',
        'shock_proof_cost' => 'double',
        'woodworking_cost' => 'double',
        'exchange_rate' => 'double',
        'discount_cost' => 'double',
        'discount_percent' => 'float',
        'amount' => 'double',
        'volume' => 'double',
        'weight' => 'double',
        'width' => 'double',
        'height' => 'double',
        'length' => 'double',
        'is_inspection' => 'bool',
        'is_insurance' => 'bool',
        'is_woodworking' => 'bool',
        'is_delivery' => 'bool',
    ];

    public function order(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return float
     */
    public function getAmountAttribute(): float
    {
        return AccountingHelper::getCosts(
            $this->shipping_cost - $this->discount_cost
        );
    }

    public function getVolumeAttribute(): float
    {
        return AccountingHelper::getCosts($this->width * $this->length * $this->height, 2);
    }

    /**
     * @return float
     */
    public function getShippingCostAttribute(): float
    {
        return AccountingHelper::getCosts(
            $this->international_shipping_cost + $this->china_shipping_cost + $this->insurance_cost + $this->inspection_cost + $this->woodworking_cost + $this->shock_proof_cost + $this->storage_cost + $this->delivery_cost
        );
    }

    public function getIsOrderAttribute()
    {
        return $this->order_type === Order::class;
    }

    public function getChinaShippingCostCnyAttribute()
    {
        return AccountingHelper::getCosts(
            $this->china_shipping_cost / ($this->exchange_rate != 0 ? $this->exchange_rate : getExchangeRate())
        );
    }

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function getOrderKindOfAttribute(): string
    {
        return !$this->order_type ? 'Chưa xác định' : ($this->order_type === Order::class ? 'Hàng Order' : 'Hàng ký gửi');
    }

    public function orderDetails(): BelongsToMany
    {
        return $this->belongsToMany(OrderDetail::class, 'order_detail_packages');
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function transporterRelation(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'transporter_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderPackageImage::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(OrderPackageNote::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            OrderDetail::class,
            'order_detail_packages',
            'order_package_id',
            'order_detail_id'
        )->withPivot(['quantity']);
    }

    public function scopeOrderCode($query)
    {
        return $query->where('order_code', request()->query('order_code'));
    }

    public function scopeCodePo($query)
    {
        return $query->where('code_po', request()->query('code_po'));
    }

    public function scopeCustomerId($query)
    {
        return $query->where('customer_id', request()->query('customer_id'));
    }

    public function scopeBillCode($query)
    {
        return $query->where('bill_code', request()->query('bill_code'));
    }

    public function scopeBillCodeAt($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('bill_code_at'), 'bill_code_at');
    }

    public function scopeCustomerCode($query)
    {
        return $query->whereHas('customer', function($q){
           $q->where('code', request()->query('customer_code'));
        });
    }

    public function scopeCustomerPhone($query)
    {
        return $query->whereHas('customer', function($q){
            $q->where('phone_number', request()->query('customer_phone'));
        });
    }

    public function scopeCustomerName($query)
    {
        return $query->whereHas('customer', function($q){
            return $q->where('name', 'like','%'. request()->query('customer_name') .'%');
        });
    }

    public function scopeEcommerce($query)
    {
        return $query->where('ecommerce', request()->query('ecommerce'));
    }

    public function scopeCategoryId($query)
    {
        return $query->where('category_id', request()->query('category_id'));
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

    public function scopeWarehouseId($query)
    {
        return $query->where('warehouse_id', request()->query('warehouse_id'));
    }

    public function scopeWarehouseCn($query)
    {
        return $query->where('warehouse_cn', request()->query('warehouse_cn'));
    }

    public function scopeStatusExtend($query)
    {
        switch ((int)request()->query('modify_key')) {
            case 0:
                return $query->where('status', '!=' ,PackageConstant::STATUS_WAREHOUSE_VN);
            case 1:
                return $query->where('status', '!=' ,PackageConstant::STATUS_RECEIVED_GOODS);
        }
        return $query;
    }

    public function scopeType($query)
    {
        return $query->where('order_type', 'like', '%'.request()->query('type') .'%');
    }

    public function scopeModifyKey($query)
    {
        switch ((int)request()->query('modify_key'))
        {
            case 0:
                return $query->whereNull('bill_code');
            case 1:
                return $query->whereNull('code_po');
            case 2:
                return $query->where(function ($q) {
                    $q->whereNull('transporter');
                    $q->orWhereNull('transporter_id');
                });
            case 3:
                return $query->whereNull('customer_id');
            case 4:
                return $query->where('is_extension', 1);
            case 5:
                return $query->whereNotNull('bill_code')->whereNotNull('customer_id')->whereNotNull('code_po')->where(function($q){
                    $q->whereNotNull('transporter');
                    $q->orWhereNotNull('transporter');
                });
            default:
                return $query;
        }
    }

    public function scopeRequestKey($query)
    {
        switch ((int)request()->query('request_key'))
        {
            case 0:
                return $query->where('is_inspection', 1);
            case 1:
                return $query->where('is_woodworking', 1);
            case 2:
                return $query->where('is_insurance', 1);
            case 3:
                return $query->where('is_delivery', 1);
            case 4:
                return $query->where('delivery_type', OrderConstant::DELIVERY_NORMAL);
            default:
                return $query;
        }
    }
}
