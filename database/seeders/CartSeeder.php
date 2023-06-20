<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartDetail;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cart::query()->truncate();
        CartDetail::query()->truncate();
        Cart::factory(1)->create();
        CartDetail::factory(3)->create();
        $detail = CartDetail::query()->selectRaw('sum(amount_cny) as amount')->first();
        Cart::query()->first()->update(['total_amount_cny' => $detail->amount]);


    }
}
