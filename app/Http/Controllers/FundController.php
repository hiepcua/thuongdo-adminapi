<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FundService;

class FundController extends Controller
{
    public function __construct(FundService $service)
    {
        $this->_service = $service;
    }

    public function list()
    {
        // unset condition laravel scope
        // https://stackoverflow.com/questions/25312386/laravel-how-to-disable-a-global-scope-in-order-to-include-inactive-objects
        $fundModel = \App::make('App\Models\Fund');
        $funds = $fundModel->orderBy('id_number', 'asc')->withoutGlobalScopes()->get();

        if ($funds) {
            $arr_VND = [];
            $total_VND = 0;
            $arr_China = [];
            $total_China = 0;

            foreach ($funds as $key => $item) {
                $temp = [
                    'name' => $item->name,
                    'type_currency' => $item->type_currency,
                    'unit_currency' => $item->unit_currency,
                    'initial_balance' => $item->initial_balance,
                    'total_money_in' => $item->total_money_in,
                    'total_money_out' => $item->total_money_out,
                    'current_balance' => $item->current_balance,
                    'note' => $item->note,
                    'status' => $item->status,
                ];
                // Nếu là tiền VND thì  push
                if ($item->unit_currency == 1) {
                    array_push($arr_VND, $temp);
                    $total_VND += $item->current_balance;
                }
                // Nếu là tiền Yên thì  push
                if ($item->unit_currency == 2) {
                    array_push($arr_China, $temp);
                    $total_China += $item->current_balance;
                }
            }
            $data_result = [
                'arr_VND' => $arr_VND,
                'total_VND' => $total_VND,
                'arr_China' => $arr_China,
                'total_China' => $total_China,
            ];
            return resSuccessWithinData($data_result);
        }
        return resSuccess();
    }

    public function initDefault(Request $request)
    {
        $fundModel = \App::make('App\Models\Fund');

        $data = [
            ['name' => 'Vietcombank', 'type_currency' => 2, 'unit_currency' => 1],
            ['name' => 'Techcombank', 'type_currency' => 2, 'unit_currency' => 1],
            ['name' => 'BIDV', 'type_currency' => 2, 'unit_currency' => 1],
            ['name' => 'MB Bank', 'type_currency' => 2, 'unit_currency' => 1],
            ['name' => 'Tiền mặt', 'type_currency' => 1, 'unit_currency' => 1],

            ['name' => 'Công thương', 'type_currency' => 2, 'unit_currency' => 2],
            ['name' => 'Nhân dân', 'type_currency' => 2, 'unit_currency' => 2],

        ];

        $userOnline = \Auth::user();

        foreach ($data as $key => $item) {
            $fundDB = $fundModel->where('name', $item['name'])
                                ->where('organization_id', $userOnline->organization_id)
                                ->first();

            if (is_null($fundDB)) {
                $item['organization_id'] = $userOnline->organization_id;
                $fundModel->create($item);
            }
        }

        return resSuccess("OK");
    }

    public function storeMessage(): ?array
    {
       return [

       ];
    }

    public function storeRequest(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'type_currency'   => 'required|in:2,3',
            'unit_currency'   => 'required|in:1,2',
            'initial_balance' => 'required|numeric|min:0|max:10000000000',
        ];
    }
}

