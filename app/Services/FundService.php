<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Models\Fund;
use App\Http\Resources\Fund\FundResource;
use App\Http\Resources\Fund\FundListResource;
use App\Http\Resources\Fund\FundPaginateResource;

class FundService extends BaseService
{
    protected string $_paginateResource = FundPaginateResource::class;
    protected string $_listResource = FundListResource::class;
    protected string $_resource = FundResource::class;

    public function updateTotalMoney($id)
    {
        $fundModel = \App::make('App\Models\Fund');
        $fund = $fundModel->where('id', $id)->withoutGlobalScopes()->first();
        if ($fund) {
            $fundTransactionModel = \App::make('App\Models\FundTransaction');

            $fundTransactionInDB = $fundTransactionModel
                                ->where('status', 1)
                                ->where('fund_id', $id)
                                ->where('type_pay', 1)
                                ->get();

            if (is_null($fundTransactionInDB)) {
                $totalIn = 0;
            } else {
                $totalIn = array_sum(\Arr::pluck($fundTransactionInDB, 'money'));
            }

            $fundTransactionOutDB = $fundTransactionModel
                                ->where('status', 1)
                                ->where('fund_id', $id)
                                ->where('type_pay', 2)
                                ->get();

            if (is_null($fundTransactionOutDB)) {
                $totalOut = 0;
            } else {
                $totalOut = array_sum(\Arr::pluck($fundTransactionOutDB, 'money'));
            }


            $fund->total_money_in = $totalIn;
            $fund->total_money_out = $totalOut;
            $fund->current_balance = $totalIn - $totalOut;
            $fund->save();

            // Thêm trả ra Balance hiện tại
            return $fund->current_balance;
        }
        return -1;
    }

    public function getBalanceFundChina(): float
    {
        return Fund::query()->where(['type_currency' => 2, 'unit_currency' => 2])->sum('current_balance');
    }
}
