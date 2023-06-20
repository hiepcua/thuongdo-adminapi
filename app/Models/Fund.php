<?php

namespace App\Models;
use App\Scopes\Traits\Filterable;

class Fund extends BaseModel
{
    use Filterable;

    public $fillable = [
        'name',
        'organization_id',
        'type_currency',
        'unit_currency',
        'initial_balance',
        'total_money_in',
        'total_money_out',
        'current_balance',
        'note',
        'status',
    ];

    protected $casts = ['current_balance'];

    protected $table = 'funds';
    public $timestamps = true;

    public function scopeName($query)
    {
        return $query->where('name', request()->query('name'));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTypeCurrency()
    {
        $list = [
            1 => 'Tiền mặt',
            2 => 'Tiền ngân hàng'
        ];
        return array_key_exists($this->type_currency, $list) ? $list[$this->type_currency] : 'Không xác định';
    }

    public function getUnitCurrency()
    {
        $list = [
            1 => 'VND',
            2 => '¥'
        ];
        return array_key_exists($this->unit_currency, $list) ? $list[$this->unit_currency] : 'Không xác định';
    }
}
