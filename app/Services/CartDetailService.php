<?php


namespace App\Services;


use App\Models\CartDetail;

class CartDetailService extends BaseService
{
    /**
     * @param  CartDetail  $detail
     */
    public function updateAmountByQuantity(CartDetail $detail): void
    {
        $detail->amount_cny = $detail->quantity * $detail->unit_price_cny;
        $detail->save();
    }
}