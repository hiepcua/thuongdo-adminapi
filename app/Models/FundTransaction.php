<?php

namespace App\Models;
use App\Scopes\Traits\Filterable;

use App\Services\FilterService;

class FundTransaction extends BaseModel
{
    use Filterable;

    protected string $_tableNameFriendly = 'Phiếu thu chi';
    protected $_prefixRoute = 'fund_transaction';

    public $fillable = [
        'organization_id',
        'user_create_id',

        'code',

        'type_object', // Đối tượng
        'type_pay', // Loại thanh toán thu hay chi
        'fund_type_pay_id', // Chi tiết thanh toán nó là cái gì
        'fund_type_pay_code', // Chi tiết thanh toán nó là cái gì (mã đặc biệt)

        'fund_id', // Quỹ nào
        'fund_name', // Tên quỹ
        'fund_type_currency', // Loại sổ là gì (tiền mặt hay ngân hàng)
        'fund_unit_currency', // Tiền này là loại gì: Việt Nam hay TQ

        'customer_code',
        'customer_name',
        'customer_phone',

        'money',
        'money_update',
        'balance',

        'fee_customer',
        'fee_customer_ratio',
        'fee_system',
        'fee_system_ratio',
        'cn_change',
        'cn_change_ratio',
        'fee_change_cn',
        'fee_change_vnd',

        'fund_transaction_id',
        'fund_transaction_update_id',
        'customer_withdrawal_id',

        'note',
        'logs',
        'status',
        'lock',
    ];

    protected $table = 'fund_transactions';
    public $timestamps = true;

    public function scopeCustomerCode($query)
    {
        return $query->where('customer_code', request()->query('customer_code'));
    }

    public function scopeCustomerName($query)
    {
        $customer_name = request()->query('customer_name');
        return $query->where('customer_name', 'like', "%{$customer_name}%");
    }

    public function scopeCustomerPhone($query)
    {
        return $query->where('customer_phone', request()->query('customer_phone'));
    }

    public function scopeTypeObject($query)
    {
        return $query->where('type_object', request()->query('type_object'));
    }

    public function scopeTypePay($query)
    {
        return $query->where('type_pay', request()->query('type_pay'));
    }

    public function scopeFundTypeCurrency($query)
    {
        return $query->where('fund_type_currency', request()->query('fund_type_currency'));
    }
    public function scopeFundUnitCurrency($query)
    {
        return $query->where('fund_unit_currency', request()->query('fund_unit_currency'));
    }
    public function scopeFundId($query)
    {
        return $query->where('fund_id', request()->query('fund_id'));
    }
    public function scopeStatus($query)
    {
        return $query->where('status', request()->query('status'));
    }

    public function scopeIdSort($query)
    {
        return $query->orderBy('id_number', request()->query('id_sort'));
    }

    public function scopeDate($query)
    {
        return (new FilterService())->rangeDateFilter($query, request()->query('date'), 'created_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_create_id', 'id');
    }

    public function fund_type_pay()
    {
        return $this->belongsTo(FundTypePay::class, 'fund_type_pay_id', 'id');
    }

    public function getTypeObject()
    {
        $list = [
            1 => 'Chuyển quỹ',
            2 => 'Khách hàng',
            3 => 'Nội bộ'
        ];
        return array_key_exists($this->type_object, $list) ? $list[$this->type_object] : 'Không xác định';
    }

    public function getTypePay()
    {
        $list = [
            1 => 'Khoản thu',
            2 => 'Khoản chi'
        ];
        return array_key_exists($this->type_pay, $list) ? $list[$this->type_pay] : 'Không xác định';
    }

    public function getFundTypeCurrency()
    {
        $list = [
            1 => 'Sổ quỹ tiền mặt',
            2 => 'Sổ quỹ ngân hàng'
        ];
        return array_key_exists($this->fund_type_currency, $list) ? $list[$this->fund_type_currency] : 'Không xác định';
    }

}
