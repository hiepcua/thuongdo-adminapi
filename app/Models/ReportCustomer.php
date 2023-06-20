<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ReportCustomer
 * @package App\Models
 *
 * @property int $status_0
 */
class ReportCustomer extends BaseModel
{
    use HasFactory;
    
    protected $casts = [
        'balance_amount' => 'float',
        'order_amount' => 'float',
        'deposited_amount' => 'float',
        'withdrawal_amount' => 'float',
        'purchase_amount' => 'float',
        'discount_amount' => 'float',
    ];
}
