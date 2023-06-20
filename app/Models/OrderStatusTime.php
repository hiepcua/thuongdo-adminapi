<?php

namespace App\Models;

use App\Constants\OrderConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatusTime extends BaseModel
{
    use HasFactory;

    protected $dates = OrderConstant::STATUSES_KEYS;
}
