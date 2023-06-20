<?php


namespace App\Services;

use Illuminate\Http\Request;
use App\Models\FundTransaction;
use App\Http\Resources\FundTransaction\FundTransactionResource;
use App\Http\Resources\FundTransaction\FundTransactionListResource;
use App\Http\Resources\FundTransaction\FundTransactionPaginateResource;

class FundTransactionService extends BaseService
{
    protected string $_paginateResource = FundTransactionPaginateResource::class;
    protected string $_listResource = FundTransactionListResource::class;
    protected string $_resource = FundTransactionResource::class;

    public function store(array $data)
    {
        $this->throwModel();
        $userOnline = \Auth::user();

        $fundModel = \App::make('App\Models\Fund');
        $fund = $fundModel->where('id', $data['fund_id'])->withoutGlobalScopes()->first();

        // Lưu thêm thông tin
        $data['fund_name'] = $fund->name;
        $data['fund_type_currency'] = $fund->type_currency;
        $data['fund_unit_currency'] = $fund->unit_currency;

        if ($data['type_object'] == 2) {
            $customerModel = \App::make('App\Models\Customer');
            if ($data['customer_code']) {
                $customer = $customerModel->where('code', $data['customer_code'])->first();
                if ($customer) {
                    $data['customer_name'] = $customer->name;
                    $data['customer_phone'] = $customer->phone_number;
                }
            }
        }

        $data['organization_id'] = $userOnline->organization_id;
        $data['user_create_id'] = $userOnline->id;
        $data['status'] = 1;
        $data['code'] = $this->generateRandomCode();
        $data['money_update'] = $data['money'];

        // Thêm log
        $temp_log = [
            'action'             => 'Tạo mới',

            'user_request_name'  => $userOnline->name,
            'user_request_email' => $userOnline->email,
            'user_request_id'    => $userOnline->id,

            'column'             => "",
            'value_old'          => "",
            'value_new'          => "",

            'time'               => date('Y-m-d H:i:s', time())
        ];

        $data['logs'] = json_encode([$temp_log]);

        // Tạo bản ghi
        $result = $this->_model->newQuery()->create($data);

        // Cập nhật số tiền tổng
        $fundService = \App::make('App\Services\FundService');;
        $balanceDB = $fundService->updateTotalMoney($data['fund_id']);

        // Ghi Balance sau khi giao dịch
        if ($balanceDB >= 0) {
            $result->balance = $balanceDB;
            $result->save();
        }

        return $result;
    }

    public function generateRandomCode() {
        $min = 100000;
        $max = 999999;
        $rand = rand($min,$max);
        return 'FD_' . date('Ymd') . '_' . $rand;
    }
}
