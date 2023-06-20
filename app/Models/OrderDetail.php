<?php

namespace App\Models;

use App\Helpers\AccountingHelper;
use App\Models\Traits\ImageAttribute;
use App\Models\Traits\OrderPackageRelation;
use App\Services\ConfigService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class OrderDetail
 * @package App\Models
 *
 * @property string $id
 * @property string $supplier_id
 * @property string $order_id
 * @property float $order_cost
 * @property float $amount_cny
 * @property float $amount
 * @property float $unit_price_cny
 * @property float $unit_price
 * @property integer $quantity
 */
class OrderDetail extends BaseModel
{
    use HasFactory, OrderPackageRelation, ImageAttribute;

    protected $casts = [
        'amount_cny' => 'float',
        'unit_price_cny' => 'float',
        'quantity' => 'integer',
    ];

    protected $appends = ['amount', 'unit_price'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getAmountAttribute(): float
    {
        return AccountingHelper::getCosts(
            $this->amount_cny * getExchangeRate($this->order_id)
        );
    }

    public function getUnitPriceAttribute(): float
    {
        return AccountingHelper::getCosts($this->amount_cny * getExchangeRate($this->order_id));
    }

    public function images(): HasMany
    {
        return $this->hasMany(OrderDetailImage::class);
    }

}
