<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CartDetail
 * @package App\Models
 *
 * @property string $id
 * @property int $quantity
 * @property float $unit_price_cny
 * @property float $amount_cny
 * @property float $order_cost
 * @property float $insurance_cost
 *
 */
class CartDetail extends BaseModel
{
    use HasFactory;
}
