<?php

namespace App\Observers;

use App\Models\CartDetail;
use App\Services\CartDetailService;

class CartDetailObserver
{
    /**
     * Handle the Customer "updated" event.
     *
     * @param  CartDetail  $detail
     * @return void
     */
    public function updated(CartDetail $detail)
    {
        (new CartDetailService())->updateAmountByQuantity($detail);
    }
}
