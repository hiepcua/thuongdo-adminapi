<?php

namespace App\Models;
use App\Scopes\Traits\Filterable;

class FundTypePay extends BaseModel
{
    use Filterable;

    protected string $_tableNameFriendly = 'Loại thu chi';
    protected $_prefixRoute = 'fund_type_pay';

    public $fillable = [
        'name',
        'organization_id',
        'type',
        'code',
        'status',
    ];

    protected $table = 'fund_type_pays';
    public $timestamps = true;

    public function getType()
    {
        $list = [
            1 => 'Phiếu thu',
            2 => 'Phiếu chi'
        ];
        return array_key_exists($this->type, $list) ? $list[$this->type] : 'Không xác định';
    }

    public function scopeName($query)
    {
        $name = request()->query('name');
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeType($query)
    {
        return $query->where('type', request()->query('type'));
    }

    public function scopeIdSort($query)
    {
        return $query->orderBy('id_number', request()->query('id_sort'));
    }

    public function scopeStatus($query)
    {
        return $query->where('status', request()->query('status'));
    }

    public function scopeHideType($query)
    {
        return $query->where('type', '>', 0);
    }

}
