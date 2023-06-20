<?php

namespace App\Models;

use App\Constants\DeliveryConstant;
use App\Constants\NoteConstant;
use App\Models\Traits\CustomerDeliveryRelation;
use App\Models\Traits\CustomerRelation;
use App\Models\Traits\Filters\StatusFilter;
use App\Models\Traits\StaffServicesRelation;
use App\Models\Traits\WarehouseRelation;
use App\Scopes\Traits\Filterable;
use App\Scopes\Traits\HasOrganization;
use App\Scopes\Traits\HasSortDescByCreated;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Delivery
 * @package App\Models
 *
 * @property string $id
 * @property string $status
 * @property DeliveryNote $notesStaff
 */
class Delivery extends BaseModel
{
    use HasFactory, HasSortDescByCreated, HasOrganization, CustomerRelation, CustomerDeliveryRelation, Filterable, StatusFilter, StaffServicesRelation, WarehouseRelation;

    protected $appends = ['custom_address', 'shipping_cost', 'amount', 'order_kind_of', 'no'];
    protected string $_tableNameFriendly = 'Giao hàng';

    protected $dates = [
        'date',
        'created_at'
    ];

    protected $casts = [
        'amount' => 'double',
        'shipping_cost' => 'float',
        'debt_cost' => 'float',
        'china_shipping_cost' => 'float',
        'international_shipping_cost' => 'float',
        'delivery_cost' => 'float',
        'insurance_cost' => 'float',
        'inspection_cost' => 'float',
        'shock_proof_cost' => 'float',
        'storage_cost' => 'float',
        'woodworking_cost' => 'float',
        'extend_cost' => 'float',
    ];

    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    public function transporterDetail(): BelongsTo
    {
        return $this->belongsTo(TransporterDetail::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function getShippingCostAttribute(): float
    {
        return $this->international_shipping_cost + $this->china_shipping_cost + $this->insurance_cost + $this->inspection_cost + $this->woodworking_cost + $this->shock_proof_cost + $this->storage_cost;
    }

    public function getAmountAttribute(): float
    {
        return $this->shipping_cost + $this->delivery_cost + $this->debt_cost;
    }

    public function getCustomAddressAttribute(): string
    {
        return $this->receiver.' - '.$this->phone_number.' - '.$this->address;
    }

    public function orderPackages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function scopePhoneNumber($query)
    {
        return $query->where('phone_number', request()->query('phone_number'));
    }

    public function scopeReceiver($query)
    {
        return $query->where('receiver', 'like' , '%' . request()->query('receiver') . '%');
    }

    public function scopePostcode($query)
    {
        return $query->where('postcode', request()->query('postcode'));
    }

    public function scopeShipperPhoneNumber($query)
    {
        return $query->where('shipper_phone_number', request()->query('shipper_phone_number'));
    }

    public function scopeTransporterId($query)
    {
        return $query->where('transporter_id', request()->query('transporter_id'));
    }

    public function scopeWarehouseId($query)
    {
        return $query->whereHas(
            'orderPackages',
            function ($q) {
                return $q->where('warehouse_id', request()->query('warehouse_id'));
            }
        );
    }

    public function scopePayment($query)
    {
        return $query->where('payment', request()->query('payment'));
    }

    public function scopeDate($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date'), 'date');
    }

    public function scopeBillCode($query)
    {
        return $query->whereHas(
            'orderPackages',
            function ($q) {
                return $q->where('bill_code', request()->query('bill_code'));
            }
        );
    }

    public function scopeTransporterDetailId($query)
    {
        return $query->where('transporter_detail_id', request()->query('transporter_detail_id'));
    }

    public function scopeIsPaidExtend($query)
    {
        return $query->where('is_paid_extend', request()->query('is_paid_extend'));
    }

    public function scopeIsReceived($query)
    {
        return $query->where('status', request()->query('is_received') ? '=' : '!=', DeliveryConstant::KEY_STATUS_DONE);
    }

    public function notesStaff(): HasMany
    {
        return $this->hasMany(DeliveryNote::class)->where('type', NoteConstant::TYPE_PRIVATE);
    }

    public function getOrderKindOfAttribute(): string
    {
        return !$this->order_type ? 'Chưa xác định' : ($this->order_type === Order::class ? 'Hàng Order' : 'Hàng ký gửi');
    }

    public function getNoAttribute(): string
    {
        return str_replace('GH_', '00', $this->code);
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'delivery_orders')->where('order_type', Order::class);
    }

    public function consignments(): BelongsToMany
    {
        return $this->belongsToMany(Consignment::class, 'delivery_orders')->where('order_type', Consignment::class);
    }
}
