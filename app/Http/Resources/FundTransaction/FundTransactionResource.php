<?php

namespace App\Http\Resources\FundTransaction;

use Illuminate\Http\Resources\Json\JsonResource;

class FundTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        $userCreate = $this->user;
        $fundTypePay = $this->fund_type_pay;

        return [
            'id'                 => $this->id,
            'user_create_name'   => !is_null($userCreate) ? $userCreate->name : '',

            'type_object'       => $this->type_object,
            'type_object_txt'   => $this->getTypeObject(),

            'type_pay'          => $this->type_pay,
            'type_pay_text'     => $this->getTypePay(),

            'fund_type_pay_id'  => $this->fund_type_pay_id,
            'fund_type_pay_code'  => $this->fund_type_pay_code,
            'fund_type_pay_txt' => !is_null($fundTypePay) ? $fundTypePay->name : '',

            'fund_id'            => $this->fund_id,
            'fund_name'          => $this->fund_name,
            'fund_type_currency' => $this->getFundTypeCurrency(),

            'customer_code'      => $this->customer_code,
            'customer_name'      => $this->customer_name,
            'customer_phone'     => $this->customer_phone,
            'customer_search'    => $this->customer_code,

            'money'              => $this->money_update,
            'balance'            => $this->balance,
            'lock'               => $this->lock,

            'logs'               => $this->logs == null ? [] : array_reverse(json_decode($this->logs)),

            'note'               => $this->note,
            'create_at'          => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null
        ];
    }
}
