<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FundTransactionService;

use Illuminate\Support\Facades\DB;

class FundTransactionController extends Controller
{
    public function __construct(FundTransactionService $service)
    {
        $this->_service = $service;
    }

    public function getCategoryVi(Request $request)
    {
        $fundModel = \App::make('App\Models\Fund');
        $funds = $fundModel->where('unit_currency', 1)->withoutGlobalScopes()->get();
        $arrFund = [];
        $arrFundCash = [];
        $arrFundBank = [];
        if ($funds) {
            foreach ($funds as $key => $fund) {
                $temp = [
                    'value' => $fund->id,
                    'name' => $fund->name,
                    'current_balance' => $fund->current_balance
                ];
                if ($fund->type_currency == 1) {
                    array_push($arrFundCash, $temp);
                }
                if ($fund->type_currency == 2) {
                    array_push($arrFundBank, $temp);
                }
                array_push($arrFund, $temp);
            }
        }

        $arrTypeObject = [
            [ 'value' => 1, 'name' => 'Chuyển quỹ' ],
            [ 'value' => 2, 'name' => 'Khách hàng' ],
            [ 'value' => 3, 'name' => 'Nội bộ' ],
        ];

        $arrTypeObjectInForm = [
            [ 'value' => 2, 'name' => 'Khách hàng' ],
            [ 'value' => 3, 'name' => 'Nội bộ' ],
        ];

        $arrTypePay = [
            [ 'value' => 1, 'name' => 'Khoản thu' ],
            [ 'value' => 2, 'name' => 'Khoản chi' ],
        ];

        $arrTypePay2 = [
            [ 'value' => 1, 'name' => 'Phiếu thu' ],
            [ 'value' => 2, 'name' => 'Phiếu chi' ],
        ];

        $arrTypeCurrency = [
            [ 'value' => 1, 'name' => 'Sổ quỹ tiền mặt' ],
            [ 'value' => 2, 'name' => 'Sổ quỹ ngân hàng' ],
        ];

        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $arrTypePayValue1 = [];
        $typePayment1 = $fundTypePayModel->where('status', 1)
                                            ->where('type', 1)
                                            ->get();
        if ($typePayment1) {
            foreach ($typePayment1 as $key => $item) {
                $temp = [
                    'value' => $item->id,
                    'name' => $item->name,
                    'code' => $item->code
                ];
                array_push($arrTypePayValue1, $temp);
            }
        }

        $arrTypePayValue2 = [];
        $typePayment2 = $fundTypePayModel->where('status', 1)
                                            ->where('type', 2)
                                            ->get();
        if ($typePayment2) {
            foreach ($typePayment2 as $key => $item) {
                $temp = [
                    'value' => $item->id,
                    'name' => $item->name
                ];
                array_push($arrTypePayValue2, $temp);
            }
        }


        $arrFundAll = [];
        $arrFundUnit = []; // Dùng để check xem nó là tiền nước nào để lấy mảng tiền tương ứng
        $arrFundAllVN = [];
        $arrFundAllChina = [];

        $fundAlls = $fundModel->withoutGlobalScopes()->get();

        if ($fundAlls) {
            foreach ($fundAlls as $key => $fund) {
                $temp = [
                    'value' => $fund->id,
                    'name' => $fund->name,
                ];
                $temp_unit= [
                    'value' => $fund->id,
                    'unit_currency' => $fund->unit_currency,
                ];
                if ($fund->unit_currency == 1) {
                    array_push($arrFundAllVN, $temp);
                }
                if ($fund->unit_currency == 2) {
                    array_push($arrFundAllChina, $temp);
                }
                array_push($arrFundAll, $temp);
                array_push($arrFundUnit, $temp_unit);
            }
        }

        $arrFundAllGroup = [
            [
                'label' => '----- Tiền Việt Nam -----',
                'options' => $arrFundAllVN,
            ],
            [
                'label' => '----- Tiền Trung Quốc -----',
                'options' => $arrFundAllChina,
            ]
        ];

        $data_result = [
            'arrTypeObject'       => $arrTypeObject,
            'arrTypeObjectInForm' => $arrTypeObjectInForm,

            'arrTypeCurrency'     => $arrTypeCurrency,

            'arrTypePay'          => $arrTypePay,
            'arrTypePay2'         => $arrTypePay2,
            'arrTypePayValue1'    => $arrTypePayValue1,
            'arrTypePayValue2'    => $arrTypePayValue2,

            'arrFund'             => $arrFund,
            'arrFundCash'         => $arrFundCash,
            'arrFundBank'         => $arrFundBank,

            // Màn hình chuyển quỹ sử dụng các key phía dưới
            'arrFundAll'          => $arrFundAll,
            'arrFundUnit'         => $arrFundUnit,
            'arrFundAllVN'        => $arrFundAllVN,
            'arrFundAllChina'     => $arrFundAllChina,
            'arrFundAllGroup'     => $arrFundAllGroup,
        ];

        return resSuccessWithinData($data_result);
    }

    public function getCategoryCn(Request $request)
    {
        $fundModel = \App::make('App\Models\Fund');
        $funds = $fundModel->where('unit_currency', 2)->get();
        $arrFundCN = [];
        if ($funds) {
            foreach ($funds as $key => $fund) {
                $temp2 = [
                    'id' => $fund->id,
                    'name' => $fund->name,
                ];
                array_push($arrFundCN, $temp2);
            }
        }


        $arrTypePay = [
            [ 'value' => 1, 'name' => 'Khoản thu' ],
            [ 'value' => 2, 'name' => 'Khoản chi' ],
        ];

        $data_result = [
            'arrFundCN'  => $arrFundCN,
            'arrTypePay' => $arrTypePay,
        ];

        return resSuccessWithinData($data_result);
    }

    public function storeMessage(): ?array
    {
       return [
        'customer_id.required_if' => 'Khách hàng không được để trống',
        'note.max'                 => 'Ghi chú không được quá 255 kí tự'
       ];
    }

    public function storeRequest(): array
    {
        return [
            'type_object'      => 'required|in:2,3',
            'type_pay'         => 'required|in:1,2',
            'fund_type_pay_id' => 'required|exists:fund_type_pays,id',
            'fund_id'          => 'required|exists:funds,id',
            'customer_code'    => 'required_if:type_object,==,2',
            'money'            => 'required|numeric|min:0|max:10000000000',
            'note'             => 'required|string|max:255',
        ];
    }

    public function store(): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__);
        $data = request()->all();
        // Nếu không phải đối tượng là khách hàng thì bỏ customer_code đi
        if ($data['type_object'] != 2) {
            unset($data['customer_code']);

            // Không cho nó chọn cái fund_type_pay_id có code bằng 1
            $fundTypePayModel = \App::make('App\Models\FundTypePay');
            $fundTypePayCode1 = $fundTypePayModel->where('code', 1)->first();
            if ($data['fund_type_pay_id'] == $fundTypePayCode1->id) {
                $result = [
                    'money' => 'Không được phép chọn khách hàng nạp tiền với loại đối tượng không phải là khách hàng'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        };

        if ($data['type_object'] == 2) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $data['customer_code'])->first();

            if (is_null($customer)) {
                $result = [
                    'customer_code' => 'Khách hàng không tồn tại trên hệ thống'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        // Kiểm tra nếu là trừ tiền thì phải có đủ tiền
        if ($data['type_pay'] == 2) {
            $fundModel = \App::make('App\Models\Fund');
            $fund = $fundModel->where('id', $data['fund_id'])->withoutGlobalScopes()->first();
            if ($fund->current_balance < $data['money']) {
                $result = [
                    'money' => 'Quỹ không đủ số dư, không thể tạo phiếu chi'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        // Kiểm tra fund_type_pay_id phải khớp với loại thu chi
        $fundTypePayModel = \App::make('App\Models\FundTypePay');
        $fundTypePayDB = $fundTypePayModel->where('type', $data['type_pay'])
                                        ->where('id', $data['fund_type_pay_id'])
                                        ->first();
        if (is_null($fundTypePayDB)) {
            $result = [
                'fund_type_pay_id' => 'Loại thu chi không hợp lệ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        $data['fund_type_pay_code'] = $fundTypePayDB->code;

        try {
            \DB::beginTransaction();

            $result = $this->_service->store($data);

            // Nếu loại thu chi là Khách hàng nạp tiền thì sinh thêm bản ghi vào ví của khách
            if ($fundTypePayDB->code == 1 && $data['type_object'] == 2) {

                $amount = $result->money;
                $content = 'Nạp tiền vào ví điện tử';
                $customer_id = $customer->id;
                $soure = $result;

                (new \App\Services\TransactionService())->setTransactionIncrement(
                    $amount,
                    \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                    $content,
                    $customer_id,
                    $soure
                );
            }

            \DB::commit();
            return resSuccessWithinData($result);
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function store30012023(): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__);
        $data = request()->all();
        // Nếu không phải đối tượng là khách hàng thì bỏ customer_code đi
        if ($data['type_object'] != 2) {
            unset($data['customer_code']);
        };

        if ($data['type_object'] == 2) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $data['customer_code'])->first();

            if (is_null($customer)) {
                $result = [
                    'customer_code' => 'Khách hàng không tồn tại trên hệ thống'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        // Kiểm tra nếu là trừ tiền thì phải có đủ tiền
        if ($data['type_pay'] == 2) {
            $fundModel = \App::make('App\Models\Fund');
            $fund = $fundModel->where('id', $data['fund_id'])->withoutGlobalScopes()->first();
            if ($fund->current_balance < $data['money']) {
                $result = [
                    'money' => 'Quỹ không đủ số dư, không thể tạo phiếu chi'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        try {
            \DB::beginTransaction();
            $result = $this->_service->store($data);
            \DB::commit();
            return resSuccessWithinData($result);
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function getCustomer()
    {
        $data = request()->all();

        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('code', $data['customer_search'])
                                ->orWhere('phone_number', $data['customer_search'])
                                ->first();

        if ($customer) {
            $result = [
                'customer_has' => 1,
                'customer_code' => $customer->code,
                'customer_phone' => $customer->phone_number,
                'customer_name' => $customer->name,
            ];
            return resSuccessWithinData($result);
        }

        $result = [
            'customer_has' => 0,
            'customer_code' => "",
            'customer_phone' => "",
            'customer_name' => "",
        ];
        return resSuccessWithinData($result);
    }

    public function hide(string $id)
    {
        $data = request()->all();
        $fundTransactionModel = \App::make('App\Models\FundTransaction');
        $fundTransaction = $fundTransactionModel->where('id', $id)->where('status', 1)->first();
        if (is_null($fundTransaction)) {
            return resError('Bản ghi không tồn tại');
        }
        try {
            \DB::beginTransaction();

            // Trừ tiền của quỹ
            $fundTypePayModel = \App::make('App\Models\FundTypePay');
            $fundTypeHoanTien = $fundTypePayModel->where('code', 6)->first();
            if (is_null($fundTypeHoanTien)) {
                $result = [
                    'unit' => 'Loại thanh toán hoàn tiền chưa được khởi tạo, liên hệ admin'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }

            $fundService = \App::make('App\Services\FundService');

            // Nếu loại là nạp tiền cho khách hàng

            $type_object        = $fundTransaction->type_object; // Loại là khách hàng
            $type_pay           = $fundTransaction->type_pay; // Hình thức là thu tiền
            $fund_type_pay_code = $fundTransaction->fund_type_pay_code; // Loại là thu tiền khách hàng

            if ($type_object == 2 && $type_pay == 1 && $fund_type_pay_code == 1) {
                // Kiểm tra số dư ví của khách có đủ để thực hiện xóa cái nạp tiền đi không ?

                if ($fundTransaction->customer_code == null) {
                    $result = [
                        'money' => 'Không thể xóa vì chưa xác định được khách hàng'
                    ];
                    return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                }

                $customerModel = \App::make('App\Models\Customer');
                $customer = $customerModel->where('code', $fundTransaction->customer_code)->first();

                $customerService = \App::make('App\Services\CustomerService');;
                $customerBalance = (int)$customerService->getBalanceAmount($customer->id);

                if ($customerBalance < $fundTransaction->money) {
                    $result = [
                        'money' => 'Số dư ví của khách không đủ để xóa nạp tiền'
                    ];
                    return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                }

                // Kiểm tra xem số dư của quỹ có đủ không á

                $fundModel = \App::make('App\Models\Fund');
                $fundDB = $fundModel->where('id', $fundTransaction->fund_id)->first();

                if ($fundDB && $fundTransaction->money > $fundDB->current_balance) {
                    $result = [
                        'unit' => 'Số dư quỹ không đủ để xóa nạp tiền'
                    ];
                    return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                }

                // Tạo 1 phiếu trừ tiền của khách

                // Trừ tiền của khách
                $amount = (int) $fundTransaction->money;
                $content = 'Hủy nạp tiền vào ví điện tử';
                $customer_id = $customer->id;
                $soure = $fundTransaction;

                (new \App\Services\TransactionService())->setTransactionDecrement(
                    $amount,
                    \App\Constants\TransactionConstant::STATUS_WITHDRAWAL,
                    $content,
                    $customer_id,
                    $soure
                );
            }

            // Nếu là chuyễn quỹ
            if ($type_object == 1 && $fund_type_pay_code == 3) {
                // Tìm cái bản ghi liên quan
                $fundTransactionRelation = $fundTransactionModel->where('id', $fundTransaction->fund_transaction_id)->first();
                if ($fundTransactionRelation) {

                    // Check số dư
                    $fund_id = $fundTransaction->fund_id;
                    if ($fundTransactionRelation->type_pay == 1) {
                        $fund_id = $fundTransactionRelation->fund_id;
                    }

                    $fundModel = \App::make('App\Models\Fund');
                    $fundDB = $fundModel->where('id', $fund_id)->first();

                    if ($fundDB && $fundTransaction->money > $fundDB->current_balance) {
                        $result = [
                            'unit' => 'Số dư quỹ không đủ để xóa nạp tiền'
                        ];
                        return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                    }

                    $this->rollbackBalance($fundTransactionRelation, $fundTypeHoanTien);
                }
            }

            // Nếu là xóa nạp tiền TQ
            if ($type_object == 3 && $fund_type_pay_code == 4) {
                // Kiểm tra xem số dư của quỹ có đủ không á

                $fundModel = \App::make('App\Models\Fund');
                $fundDB = $fundModel->where('id', $fundTransaction->fund_id)->first();

                if ($fundDB && $fundTransaction->money > $fundDB->current_balance) {
                    $result = [
                        'unit' => 'Số dư quỹ TQ không đủ để xóa nạp tiền'
                    ];
                    return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                }

                // Nếu là giao dịch nhận tiền của Trung thì tìm toàn bộ các bản ghi của Việt để ẩn
                if ($type_pay == 1) {
                    $fundTransactionRelations = $fundTransactionModel->where('fund_transaction_id', $id)->get();

                    if (count($fundTransactionRelations)) {
                        foreach ($fundTransactionRelations as $key => $item) {
                            $this->rollbackBalance($item, $fundTypeHoanTien);
                        }
                    }
                }

                // Nếu là chi ra thì phải tìm các bản ghi đồng hành + bản ghi bên Trung
                if ($type_pay == 2) {
                    $fundTransactionRelations = $fundTransactionModel
                                        ->where('fund_transaction_id', $fundTransaction->fund_transaction_id)
                                        ->whereNotIn('id', [$fundTransaction->id])
                                        ->get();

                    if (count($fundTransactionRelations)) {
                        foreach ($fundTransactionRelations as $key => $item) {
                            $this->rollbackBalance($item, $fundTypeHoanTien);
                        }
                    }

                    $fundTransactionRelation = $fundTransactionModel->where('id', $fundTransaction->fund_transaction_id)->first();

                    if ($fundTransactionRelation) {
                        $this->rollbackBalance($fundTransactionRelation, $fundTypeHoanTien);
                    }

                }
            }

            // Nếu là đổi tiền
            if ($type_object == 2 && $type_pay == 2 && $fund_type_pay_code == 2) {
                $customerModel = \App::make('App\Models\Customer');
                $customer = $customerModel->where('code', $fundTransaction->customer_code)->first();

                // Trả tiền lại lại trả khách
                $amount = ($fundTransaction->cn_change + $fundTransaction->fee_customer) * $fundTransaction->fee_customer_ratio;
                $content = 'Hoàn tiền Đổi tiền Việt sang tiền Trung';
                $customer_id = $customer->id;
                $soure = $fundTransaction;

                (new \App\Services\TransactionService())->setTransactionIncrement(
                    $amount,
                    \App\Constants\TransactionConstant::STATUS_REFUND,
                    $content,
                    $customer_id,
                    $soure
                );
            }

            // Nếu là xóa rút tiền
            if ($type_object == 2 && $type_pay == 2 && $fund_type_pay_code == 5) {
                $customerModel = \App::make('App\Models\Customer');
                $customer = $customerModel->where('code', $fundTransaction->customer_code)->first();

                // Update cái yêu cầu rút tiền về đã hủy
                $customerWithdrawalModel = \App::make('App\Models\CustomerWithdrawal');
                $customerWithdrawalDB = $customerWithdrawalModel->where('id', $fundTransaction->customer_withdrawal_id)->first();
                if ($customerWithdrawalDB) {
                    $customerWithdrawalDB->status = \App\Constants\CustomerConstant::KEY_WITHDRAWAL_STATUS_CANCEL;
                    $customerWithdrawalDB->save();
                }

                // Trả tiền lại lại trả khách
                $amount = (int)$customerWithdrawalDB->amount;
                $content = 'Hoàn tiền Khách hàng rút tiền';
                $customer_id = $customer->id;
                $soure = $fundTransaction;

                (new \App\Services\TransactionService())->setTransactionIncrement(
                    $amount,
                    \App\Constants\TransactionConstant::STATUS_REFUND,
                    $content,
                    $customer_id,
                    $soure
                );
            }

            $this->rollbackBalance($fundTransaction, $fundTypeHoanTien);

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function rollbackBalance($fundTransaction, $fundTypeHoanTien)
    {
        $data_fund = [
            'type_object'        => $fundTransaction->type_object,
            'customer_code'      => $fundTransaction->customer_code,
            'type_pay'           => $fundTransaction->type_pay == 1 ? 2 : 1,
            'fund_id'            => $fundTransaction->fund_id,

            'fund_type_pay_id'   => $fundTypeHoanTien->id,
            'fund_type_pay_code' => $fundTypeHoanTien->code,

            'money'              => $fundTransaction->money,
            'note'               => 'Hủy ' . $fundTransaction->note,
            'lock'               => 1
        ];

        $result = $this->_service->store($data_fund);

        // Khóa nó lại
        $fundTransaction->lock = 1;
        $fundTransaction->save();

        // Thêm ghi log
        $userOnline = \Auth::user();
        $error_logsDB = $fundTransaction->logs == null ? [] : json_decode($fundTransaction->logs);

        $temp_log = [
            'action'             => 'Xóa',

            'user_request_name'  => $userOnline->name,
            'user_request_email' => $userOnline->email,
            'user_request_id'    => $userOnline->id,

            'column'             => "",
            'value_old'          => "",
            'value_new'          => "",

            'time'               => date('Y-m-d H:i:s', time())
        ];

        array_push($error_logsDB, $temp_log);

        $fundTransaction->logs = json_encode($error_logsDB);
        $fundTransaction->save();
    }

    public function hide01022023(string $id)
    {
        $data = request()->all();
        $fundTransactionModel = \App::make('App\Models\FundTransaction');
        $fundTransaction = $fundTransactionModel->where('id', $id)->where('status', 1)->first();
        if (is_null($fundTransaction)) {
            return resError('Bản ghi không tồn tại');
        }
        try {
            \DB::beginTransaction();
            // Cho nó ẩn đi
            $fundTransaction->status = 0;
            $fundTransaction->save();

            // Cập nhật số tiền tổng
            $fundService = \App::make('App\Services\FundService');;
            $fundService->updateTotalMoney($fundTransaction->fund_id);

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function updateMessage(): array
    {
        return [
            'customer_code.required' => 'Khách hàng không được để trống',
            'note.max'                => 'Ghi chú không được quá 255 kí tự'
        ];
    }

    public function updateRequest(string $id): array
    {
        return [
            'customer_code'    => 'required|exists:customers,code',
            'money_update'            => 'required|numeric|min:1|max:10000000000',
        ];
    }

    public function update(string $id): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__, $id);

        $data = request()->only([
            'customer_code',
            'money_update'
        ]);

        $fundTransactionModel = \App::make('App\Models\FundTransaction');
        $fundTransaction = $fundTransactionModel->where('id', $id)
                                            ->where('fund_type_pay_code', 1)
                                            ->where('lock', 0)->first();
        if (is_null($fundTransaction)) {
            return resError('Bản ghi không tồn tại');
        }

        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('code', $data['customer_code'])->first();

        $data['customer_name'] = $customer->name;
        $data['customer_phone'] = $customer->phone_number;

        try {
            \DB::beginTransaction();

            $fundTypePayModel = \App::make('App\Models\FundTypePay');
            $fundTypeCapNhat = $fundTypePayModel->where('code', 7)->first();
            if (is_null($fundTypeCapNhat)) {
                $result = [
                    'unit' => 'Loại thanh cập nhật số dư chưa được khởi tạo, liên hệ admin'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }


            // Nếu chưa có khách nào thì xử lý bình thường
            if ($fundTransaction->customer_code == null) {
                $result = $this->_service->update($id,$data);

                // Tạo giao dịch nạp tiền cho khách
                $amount = $result->money;
                $content = 'Nạp tiền vào ví điện tử';
                $customer_id = $customer->id;
                $soure = $result;

                (new \App\Services\TransactionService())->setTransactionIncrement(
                    $amount,
                    \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                    $content,
                    $customer_id,
                    $soure
                );
            }

            // Nếu đã có khách hàng
            if ($fundTransaction->customer_code != null) {
                // Trường hợp khách hàng giữ nguyên và update số tiền
                if ($fundTransaction->customer_code == $data['customer_code'] && $fundTransaction->money_update != $data['money_update']) {
                    // Nếu tiền mới nhiều hơn
                    if ($data['money_update'] > $fundTransaction->money_update) {
                        $moneyDiff = $data['money_update'] - $fundTransaction->money_update;

                        // Cộng tiền vào quỹ
                        $data_fund = [
                            'type_object'                => $fundTransaction->type_object,
                            'customer_code'              => $fundTransaction->customer_code,
                            'type_pay'                   => 1,

                            'fund_id'                    => $fundTransaction->fund_id,
                            'fund_type_pay_id'           => $fundTypeCapNhat->id,
                            'fund_type_pay_code'         => $fundTypeCapNhat->code,

                            'fund_transaction_update_id' => $fundTransaction->id,

                            'money'                      => $moneyDiff,
                            'note'                       => 'Cập nhật số dư',
                            'lock'                       => 1
                        ];
                        $result = $this->_service->store($data_fund);

                        // Cộng tiền vào ví khách
                        $amount = $moneyDiff;
                        $content = $fundTransaction->note;
                        $customer_id = $customer->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionIncrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                            $content,
                            $customer_id,
                            $soure
                        );

                        // Ghi log
                        $data_log = [
                            'column' => 'Số tiền',
                            'value_old' => $fundTransaction->money_update,
                            'value_new' => $data['money_update'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        // Cập nhật số dư
                        $fundTransaction->money_update = $data['money_update'];
                        $fundTransaction->save();
                    }

                    if ($data['money_update'] < $fundTransaction->money_update) {
                        $moneyDiff = abs($data['money_update'] - $fundTransaction->money_update);

                        // Check quỹ xem đủ tiền để trừ không
                        if ($this->checkFundHasEnoughBalance($fundTransaction->fund_id, $moneyDiff) == false) {
                            $result = [
                                'unit' => 'Số dư quỹ không đủ để để cập nhật số tiền'
                            ];
                            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                        }

                        // Check khách cũ còn đủ tiền để hoàn lại không
                        if ($this->checkCustomerHasEnoughBalance($fundTransaction->customer_code, $moneyDiff) == false) {
                            $result = [
                                'money' => 'Số dư ví của khách không đủ để cập nhật số tiền'
                            ];
                            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                        }


                        // Trừ tiền vào quỹ
                        $data_fund = [
                            'type_object'                => $fundTransaction->type_object,
                            'customer_code'              => $fundTransaction->customer_code,
                            'type_pay'                   => 2,

                            'fund_id'                    => $fundTransaction->fund_id,
                            'fund_type_pay_id'           => $fundTypeCapNhat->id,
                            'fund_type_pay_code'         => $fundTypeCapNhat->code,

                            'fund_transaction_update_id' => $fundTransaction->id,

                            'money'                      => $moneyDiff,
                            'note'                       => 'Cập nhật số dư',
                            'lock'                       => 1
                        ];
                        $result = $this->_service->store($data_fund);

                        // Trừ tiền vào ví khách
                        $amount = $moneyDiff;
                        $content = "Hủy " . $fundTransaction->note;
                        $customer_id = $customer->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionDecrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_WITHDRAWAL,
                            $content,
                            $customer_id,
                            $soure
                        );

                        // Ghi log
                        $data_log = [
                            'column' => 'Số tiền',
                            'value_old' => $fundTransaction->money_update,
                            'value_new' => $data['money_update'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        // Cập nhật số dư
                        $fundTransaction->money_update = $data['money_update'];
                        $fundTransaction->save();
                    }
                }

                // Trường hợp thay đổi khách hàng và không update số tiền
                if ($fundTransaction->customer_code != $data['customer_code'] && $fundTransaction->money_update == $data['money_update']) {
                    // Check số dư của khách cũ còn đủ để trừ tiền không?
                    if ($this->checkCustomerHasEnoughBalance($fundTransaction->customer_code, $fundTransaction->money_update) == false) {
                        $result = [
                            'money' => 'Số dư ví của khách không đủ để cập nhật số tiền'
                        ];
                        return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                    }

                    // Trừ tiền ở ví khách cũ
                    $customerInFundTransaction = $customerModel->where('code', $fundTransaction->customer_code)->first();
                    $amount = $fundTransaction->money_update;
                    $content = "Hủy " . $fundTransaction->note;
                    $customer_id = $customerInFundTransaction->id;
                    $soure = $fundTransaction;

                    (new \App\Services\TransactionService())->setTransactionDecrement(
                        $amount,
                        \App\Constants\TransactionConstant::STATUS_WITHDRAWAL,
                        $content,
                        $customer_id,
                        $soure
                    );

                    // Cộng tiền vào ví khách mới
                    $amount = $fundTransaction->money_update;
                    $content = $fundTransaction->note;
                    $customer_id = $customer->id;
                    $soure = $fundTransaction;

                    (new \App\Services\TransactionService())->setTransactionIncrement(
                        $amount,
                        \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                        $content,
                        $customer_id,
                        $soure
                    );

                    // Ghi log
                    $data_log = [
                        'column' => 'Mã khách hàng',
                        'value_old' => $fundTransaction->customer_code,
                        'value_new' => $data['customer_code'],
                    ];
                    $this->writeLogFundTransaction($fundTransaction, $data_log);

                    // Cập nhật mã khách hàng
                    $fundTransaction->customer_code = $data['customer_code'];
                    $fundTransaction->customer_name = $customer->name;
                    $fundTransaction->customer_phone = $customer->phone_number;
                    $fundTransaction->save();
                }

                // Trường hợp thay đổi khách hàng và update số tiền
                if ($fundTransaction->customer_code != $data['customer_code'] && $fundTransaction->money_update != $data['money_update']) {
                    // Nếu tiền mới nhiều hơn
                    if ($data['money_update'] > $fundTransaction->money_update) {
                        $moneyDiff = $data['money_update'] - $fundTransaction->money_update;
                        // Check số dư của khách cũ còn đủ để trừ tiền không?
                        if ($this->checkCustomerHasEnoughBalance($fundTransaction->customer_code, $fundTransaction->money_update) == false) {
                            $result = [
                                'money' => 'Số dư ví của khách không đủ để cập nhật số tiền'
                            ];
                            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                        }

                        // Cộng tiền vào quỹ bênh lên cho khách mới
                        $data_fund = [
                            'type_object'                => $fundTransaction->type_object,
                            'customer_code'              => $data['customer_code'],
                            'type_pay'                   => 1,

                            'fund_id'                    => $fundTransaction->fund_id,
                            'fund_type_pay_id'           => $fundTypeCapNhat->id,
                            'fund_type_pay_code'         => $fundTypeCapNhat->code,

                            'fund_transaction_update_id' => $fundTransaction->id,

                            'money'                      => $moneyDiff,
                            'note'                       => 'Cập nhật số dư',
                            'lock'                       => 1
                        ];
                        $result = $this->_service->store($data_fund);


                        // Trừ tiền ở ví khách cũ
                        $customerInFundTransaction = $customerModel->where('code', $fundTransaction->customer_code)->first();
                        $amount = $fundTransaction->money_update;
                        $content = "Hủy " . $fundTransaction->note;
                        $customer_id = $customerInFundTransaction->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionDecrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_WITHDRAWAL,
                            $content,
                            $customer_id,
                            $soure
                        );

                        // Cộng tiền mới cho khách B
                        $amount = $data['money_update'];
                        $content = $fundTransaction->note;
                        $customer_id = $customer->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionIncrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                            $content,
                            $customer_id,
                            $soure
                        );

                        // Ghi log
                        $data_log = [
                            'column' => 'Số tiền',
                            'value_old' => $fundTransaction->money_update,
                            'value_new' => $data['money_update'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        $data_log = [
                            'column' => 'Mã khách hàng',
                            'value_old' => $fundTransaction->customer_code,
                            'value_new' => $data['customer_code'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        // Cập nhật số dư
                        $fundTransaction->money_update = $data['money_update'];
                        $fundTransaction->customer_code = $data['customer_code'];
                        $fundTransaction->customer_name = $customer->name;
                        $fundTransaction->customer_phone = $customer->phone_number;
                        $fundTransaction->save();
                    }

                    if ($data['money_update'] < $fundTransaction->money_update) {
                        $moneyDiff = abs($data['money_update'] - $fundTransaction->money_update);
                        // Check quỹ xem đủ tiền để trừ không
                        if ($this->checkFundHasEnoughBalance($fundTransaction->fund_id, $moneyDiff) == false) {
                            $result = [
                                'unit' => 'Số dư quỹ không đủ để để cập nhật số tiền'
                            ];
                            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
                        }

                        // Trừ tiền vào quỹ bị thiếu hụt cho khách cũ
                        $data_fund = [
                            'type_object'                => $fundTransaction->type_object,
                            'customer_code'              => $fundTransaction->customer_code,
                            'type_pay'                   => 2,

                            'fund_id'                    => $fundTransaction->fund_id,
                            'fund_type_pay_id'           => $fundTypeCapNhat->id,
                            'fund_type_pay_code'         => $fundTypeCapNhat->code,

                            'fund_transaction_update_id' => $fundTransaction->id,

                            'money'                      => $moneyDiff,
                            'note'                       => 'Cập nhật số dư',
                            'lock'                       => 1
                        ];
                        $result = $this->_service->store($data_fund);

                        // Trừ tiền ở ví khách cũ
                        $customerInFundTransaction = $customerModel->where('code', $fundTransaction->customer_code)->first();
                        $amount = $fundTransaction->money_update;
                        $content = "Hủy " . $fundTransaction->note;
                        $customer_id = $customerInFundTransaction->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionDecrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_WITHDRAWAL,
                            $content,
                            $customer_id,
                            $soure
                        );


                        // Cộng tiền mới cho khách B
                        $amount = $data['money_update'];
                        $content = $fundTransaction->note;
                        $customer_id = $customer->id;
                        $soure = $fundTransaction;

                        (new \App\Services\TransactionService())->setTransactionIncrement(
                            $amount,
                            \App\Constants\TransactionConstant::STATUS_DEPOSIT,
                            $content,
                            $customer_id,
                            $soure
                        );


                        // Ghi log
                        $data_log = [
                            'column' => 'Số tiền',
                            'value_old' => $fundTransaction->money_update,
                            'value_new' => $data['money_update'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        $data_log = [
                            'column' => 'Mã khách hàng',
                            'value_old' => $fundTransaction->customer_code,
                            'value_new' => $data['customer_code'],
                        ];
                        $this->writeLogFundTransaction($fundTransaction, $data_log);

                        // Cập nhật số dư
                        $fundTransaction->money_update = $data['money_update'];
                        $fundTransaction->customer_code = $data['customer_code'];
                        $fundTransaction->customer_name = $customer->name;
                        $fundTransaction->customer_phone = $customer->phone_number;
                        $fundTransaction->save();
                    }
                }
            }

            // dd('xem xét');
            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function checkFundHasEnoughBalance($fund_id, $money)
    {
        $fundModel = \App::make('App\Models\Fund');
        $fundDB = $fundModel->where('id', $fund_id)->first();

        if ($fundDB && $fundDB->current_balance > $money) {
            return true;
        }
        return false;
    }

    public function checkCustomerHasEnoughBalance($customer_code, $money)
    {
        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('code', $customer_code)->first();

        $customerService = \App::make('App\Services\CustomerService');;
        $customerBalance = (int)$customerService->getBalanceAmount($customer->id);

        if ($customerBalance >= $money) {
            return true;
        }
        return false;
    }


    public function writeLogFundTransaction($fundTransaction, $data)
    {
        $userOnline = \Auth::user();
        $error_logsDB = $fundTransaction->logs == null ? [] : json_decode($fundTransaction->logs);

        $temp_log = [
            'action'             => 'Cập nhật',

            'user_request_name'  => $userOnline->name,
            'user_request_email' => $userOnline->email,
            'user_request_id'    => $userOnline->id,

            'column'             => $data['column'],
            'value_old'          => $data['value_old'],
            'value_new'          => $data['value_new'],

            'time'               => date('Y-m-d H:i:s', time())
        ];

        array_push($error_logsDB, $temp_log);

        $fundTransaction->logs = json_encode($error_logsDB);
        $fundTransaction->save();
    }

    public function saveTransferFund(Request $request)
    {
        $validationRules = [
            'fund_id_from' => 'required|exists:funds,id',
            'fund_id_to'   => 'required|exists:funds,id',
            'money'        => 'required|numeric|min:0|max:10000000000'
        ];
        $validationMessages = [
            'money.max' => 'Alo'
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra 2 quỹ phải khách nhau
        if ($data['fund_id_from'] == $data['fund_id_to']) {
            $result = [
                'unit' => 'Không thể chọn quỹ từ và quỹ đến giống nhau'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra 2 kho phải cùng 1 loại tiền tệ
        $fundModel = \App::make('App\Models\Fund');

        $fund_from = $fundModel->where('id', $data['fund_id_from'])->withoutGlobalScopes()->first();
        $fund_to = $fundModel->where('id', $data['fund_id_to'])->withoutGlobalScopes()->first();

        if ($fund_from->unit_currency != $fund_to->unit_currency) {
            $result = [
                'unit' => 'Hai quỹ không cùng đơn vị tiền tệ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra số tiền chi ra phải có
        if ($data['money'] > $fund_from->current_balance) {
            $result = [
                'money' => 'Quỹ ' . $fund_from->name . ' không đủ số dư, không thể tạo chuyển quỹ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Lấy ra FundTypePay của chuyển quỹ
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $fundChuyenQuy = $fundTypePayModel->where('code', 3)->first();

        if (is_null($fundChuyenQuy)) {
            $result = [
                'unit' => 'Loại thanh toán chuyển quỹ chưa được khởi tạo, liên hệ admin'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Tạo phiếu chi
        $data_from = [
            'type_object'        => 1,
            'type_pay'           => 2,
            'fund_id'            => $fund_from->id,
            'fund_type_pay_id'   => $fundChuyenQuy->id,
            'fund_type_pay_code' => $fundChuyenQuy->code,

            'money'              => $data['money'],
            'note'               => "Chuyển tiền tới quỹ " . $fund_to->name,
        ];

        $data_to = [
            'type_object'        => 1,
            'type_pay'           => 1,
            'fund_id'            => $fund_to->id,
            'fund_type_pay_id'   => $fundChuyenQuy->id,
            'fund_type_pay_code' => $fundChuyenQuy->code,

            'money'              => $data['money'],
            'note'               => "Nhận tiền từ quỹ " . $fund_from->name,
        ];

        try {
            \DB::beginTransaction();

            // Tạo bản ghi
            $result_from = $this->_service->store($data_from);
            $result_to = $this->_service->store($data_to);

            // Cập nhật mã giao dịch của nhau
            $result_from->fund_transaction_id = $result_to->id;
            $result_from->save();

            $result_to->fund_transaction_id = $result_from->id;
            $result_to->save();

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function saveTransferFund01022023(Request $request)
    {
        $validationRules = [
            'fund_id_from' => 'required|exists:funds,id',
            'fund_id_to'   => 'required|exists:funds,id',
            'money'        => 'required|numeric|min:0|max:10000000000'
        ];
        $validationMessages = [
            'money.max' => 'Alo'
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra 2 quỹ phải khách nhau
        if ($data['fund_id_from'] == $data['fund_id_to']) {
            $result = [
                'unit' => 'Không thể chọn quỹ từ và quỹ đến giống nhau'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra 2 kho phải cùng 1 loại tiền tệ
        $fundModel = \App::make('App\Models\Fund');

        $fund_from = $fundModel->where('id', $data['fund_id_from'])->withoutGlobalScopes()->first();
        $fund_to = $fundModel->where('id', $data['fund_id_to'])->withoutGlobalScopes()->first();

        if ($fund_from->unit_currency != $fund_to->unit_currency) {
            $result = [
                'unit' => 'Hai quỹ không cùng đơn vị tiền tệ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra số tiền chi ra phải có
        if ($data['money'] > $fund_from->current_balance) {
            $result = [
                'money' => 'Quỹ ' . $fund_from->name . ' không đủ số dư, không thể tạo chuyển quỹ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Tạo phiếu chi
        $data_from = [
            'type_object'        => 1,

            'type_pay'           => 2,
            'fund_type_pay_id'   => null,

            'fund_id'            => $fund_from->id,
            'fund_type_currency' => $fund_from->type_currency,
            'fund_unit_currency' => $fund_from->unit_currency,

            'money'              => $data['money'],
            'note'               => "Chuyển tiền tới quỹ " . $fund_to->name,
        ];

        $data_to = [
            'type_object'        => 1,

            'type_pay'           => 1,
            'fund_type_pay_id'   => null,

            'fund_id'            => $fund_to->id,
            'fund_type_currency' => $fund_to->type_currency,
            'fund_unit_currency' => $fund_to->unit_currency,

            'money'              => $data['money'],
            'note'               => "Nhận tiền từ quỹ " . $fund_from->name,
        ];


        try {
            \DB::beginTransaction();

            // Tạo bản ghi
            $result_from = $this->_service->store($data_from);
            $result_to = $this->_service->store($data_to);

            // Cập nhật số tiền tổng
            $fundService = \App::make('App\Services\FundService');
            $fundService->updateTotalMoney($fund_from->id);
            $fundService->updateTotalMoney($fund_to->id);

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function saveMoneyChange(Request $request)
    {
        $validationRules = [
            'customer_code'      => 'required|exists:customers,code',

            'cn_change'          => 'required|numeric|min:1|max:10000000000',

            'fee_customer'       => 'required|numeric|min:1|max:10000000000',
            'fee_customer_ratio' => 'required|numeric|min:1|max:10000000000',
            'fee_system'         => 'required|numeric|min:0|max:10000000000',
            'fee_system_ratio'   => 'required|numeric|min:0|max:10000000000',

            'fund_id'            => 'required|exists:funds,id',
        ];
        $validationMessages = [
            'fee_customer.min'       => 'Phí khách hàng tối thiểu là 1',
            'fee_customer_ratio.min' => 'Tỉ giá Phí khách hàng tối thiểu là 1',
            'fee_system.min'         => 'Phí hệ thống tối thiểu là 0',
            'fee_system_ratio.min'   => 'Tỉ giá Phí hệ thống tối thiểu là 0',
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra xem ngân hàng xuất tiền đúng là của TQ không
        $fundModel = \App::make('App\Models\Fund');

        $fundDB = $fundModel->where('id', $data['fund_id'])
                                ->where('unit_currency', 2)
                                ->first();
        if (is_null($fundDB)) {
            $result = [
                'unit' => 'Đây không phải quỹ TQ, vui lòng kiểm tra lại'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra xem nó có đủ tiền để thực hiện giao dịch này này không
        if (($data['cn_change'] + $data['fee_system']) > $fundDB->current_balance) {
            $result = [
                'money' => 'Quỹ ' . $fundDB->name . ' không đủ số dư'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Tính tổng tiền khách phải trả để đổi
        $configModel = \App::make('App\Models\Config');
        $exchange_rateDB = $configModel->where('key', 'exchange_rate')->first();

        if (is_null($exchange_rateDB)) {
            $result = [
                'money' => 'Không lấy được tỉ giá'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }
        $exchange_rate = (int) $exchange_rateDB->value;

        $total_money = ($data['cn_change'] + $data['fee_customer']) * $data['fee_customer_ratio'];

        // Kiểm tra xem khách hàng có đủ số dư để trừ không
        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('code', $data['customer_code'])->first();

        $customerService = \App::make('App\Services\CustomerService');;
        $customerBalance = (int)$customerService->getBalanceAmount($customer->id);

        if ($customerBalance < $total_money) {
            $result = [
                'money' => 'Số dư ví của khách không đủ để thực hiện giao dịch'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Lấy ra FundTypePay nạp tiền TQ
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $fundChi = $fundTypePayModel->where('code', 2)->first();

        if (is_null($fundChi)) {
            $result = [
                'unit' => 'Loại thanh toán chi Đổi tiền chưa được khởi tạo, liên hệ admin'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        try {
            \DB::beginTransaction();

            // Tạo phiếu chi cho quỹ
            $dataChiQuy = [
                'type_object'        => 2,
                'customer_code'      => $data['customer_code'],

                'type_pay'           => 2,
                'fund_type_pay_id'   => $fundChi->id,
                'fund_type_pay_code' => $fundChi->code,

                'fund_id'            => $fundDB->id,

                'money'              => $data['cn_change'] + $data['fee_system'],

                'fee_customer'       => $data['fee_customer'],
                'fee_customer_ratio' => $data['fee_customer_ratio'],
                'fee_system'         => $data['fee_system'],
                'fee_system_ratio'   => $data['fee_system_ratio'],

                'cn_change'          => $data['cn_change'],

                'note'               => "Đổi tiền cho khách hàng " . $customer->code,
            ];

            $result_1 = $this->_service->store($dataChiQuy);

            // Trừ tiền của khách
            $amount = $total_money;
            $content = 'Đổi tiền Việt sang tiền Trung';
            $customer_id = $customer->id;
            $soure = $result_1;

            (new \App\Services\TransactionService())->setTransactionDecrement(
                $amount,
                \App\Constants\TransactionConstant::STATUS_EXCHANGE,
                $content,
                $customer_id,
                $soure
            );

            \DB::commit();
            return resSuccessWithinData($result_1);
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function saveMoneyChange31012022(Request $request)
    {
        $validationRules = [
            'customer_code'      => 'required|exists:customers,code',

            'cn_change'          => 'required|numeric|min:1|max:10000000000',

            'fee_customer'       => 'required|numeric|min:1|max:10000000000',
            'fee_customer_ratio' => 'required|numeric|min:1|max:10000000000',
            'fee_system'         => 'required|numeric|min:1|max:10000000000',
            'fee_system_ratio'   => 'required|numeric|min:1|max:10000000000',

            'fund_id'            => 'required|exists:funds,id',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra xem ngân hàng xuất tiền đúng là của TQ không
        $fundModel = \App::make('App\Models\Fund');

        $fundDB = $fundModel->where('id', $data['fund_id'])
                                ->where('unit_currency', 2)
                                ->first();
        if (is_null($fundDB)) {
            $result = [
                'unit' => 'Đây không phải quỹ TQ, vui lòng kiểm tra lại'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra xem nó có đủ tiền để thực hiện giao dịch này này không
        if ($data['cn_change'] > $fundDB->current_balance) {
            $result = [
                'money' => 'Quỹ ' . $fundDB->name . ' không đủ số dư'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Tính tổng tiền khách phải trả để đổi
        $configModel = \App::make('App\Models\Config');
        $exchange_rateDB = $configModel->where('key', 'exchange_rate')->first();

        if (is_null($exchange_rateDB)) {
            $result = [
                'money' => 'Không lấy được tỉ giá'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }
        $exchange_rate = (int) $exchange_rateDB->value;

        $total_money = $data['cn_change'] * $exchange_rate;
        $total_money +=  $data['fee_customer'] * $data['fee_customer_ratio'];
        $total_money +=  $data['fee_system'] * $data['fee_system_ratio'];

        // Kiểm tra xem khách hàng có đủ số dư để trừ không
        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('code', $data['customer_code'])->first();

        $customerService = \App::make('App\Services\CustomerService');;
        $customerBalance = (int)$customerService->getBalanceAmount($customer->id);

        if ($customerBalance < $total_money) {
            $result = [
                'money' => 'Số dư ví của khách không đủ để thực hiện giao dịch'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Lấy ra FundTypePay nạp tiền TQ
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $fundChi = $fundTypePayModel->where('code', 2)->first();

        if (is_null($fundChi)) {
            $result = [
                'unit' => 'Loại thanh toán chi Đổi tiền chưa được khởi tạo, liên hệ admin'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        try {
            \DB::beginTransaction();

            // Tạo phiếu chi cho quỹ
            $dataChiQuy = [
                'type_object'        => 2,
                'customer_code'      => $data['customer_code'],

                'type_pay'           => 2,
                'fund_type_pay_id'   => $fundChi->id,

                'fund_id'            => $fundDB->id,
                'fund_type_currency' => $fundDB->type_currency,
                'fund_unit_currency' => $fundDB->unit_currency,

                'money'              => $data['cn_change'],

                'fee_customer'       => $data['fee_customer'],
                'fee_customer_ratio' => $data['fee_customer_ratio'],
                'fee_system'         => $data['fee_system'],
                'fee_system_ratio'   => $data['fee_system_ratio'],

                'cn_change'          => $data['cn_change'],
                'cn_change_ratio'    => $exchange_rate,

                'note'               => "Đổi tiền cho khách hàng " . $customer->code,
            ];

            $result_1 = $this->_service->store($dataChiQuy);

            // Trừ tiền của khách
            $amount = $total_money;
            $content = 'Đổi tiền Việt sang tiền Trung';
            $customer_id = $customer->id;
            $soure = $result_1;

            (new \App\Services\TransactionService())->setTransactionDecrement(
                $amount,
                \App\Constants\TransactionConstant::STATUS_EXCHANGE,
                $content,
                $customer_id,
                $soure
            );

            \DB::commit();
            return resSuccessWithinData($result_1);
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function saveMoneyReCharge(Request $request)
    {
        $validationRules = [
            'cn_change'            => 'required|numeric|min:0|max:10000000000',
            'cn_change_ratio'      => 'required|numeric|min:0|max:10000000000',

            'fee_change_cn'        => 'required|numeric|min:0|max:10000000000',
            'fee_change_vnd'       => 'required|numeric|min:0|max:10000000000',

            'fund_id_from'         => 'required|array',
            'fund_id_from.*.id'    => 'required|exists:funds,id',
            'fund_id_from.*.money' => 'required|numeric|min:1|max:10000000000',

            'fund_id_to'           => 'required|exists:funds,id',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra toàn bộ các quỹ chi ra là từ quỹ VN
        $fundModel = \App::make('App\Models\Fund');
        foreach ($data['fund_id_from'] as $key => $fund) {
            $fundDB = $fundModel->where('id', $fund['id'])
                                    ->where('unit_currency', 1)
                                    ->first();
            if (is_null($fundDB)) {
                $result = [
                    'unit' => 'Quỹ chi tiền phải là quỹ VN'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }

            // Kiểm tra xem quỹ ý đủ tiền chi ra khônng
            if ($fund['money'] > $fundDB->current_balance) {
                $result = [
                    'money' => 'Quỹ ' . $fundDB->name . ' không đủ số dư'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        // Kiểm tra quỹ nhận tiền phải là quỹ China
        $fundDB = $fundModel->where('id', $data['fund_id_to'])
                                ->where('unit_currency', 2)
                                ->first();

        if (is_null($fundDB)) {
            $result = [
                'unit' => 'Quỹ nhận không phải quỹ TQ, vui lòng kiểm tra lại'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra tồng tiền nó có khớp không
        $total_money = ($data['cn_change']  + $data['fee_change_cn']) * $data['cn_change_ratio'] + $data['fee_change_vnd'];

        $total_money_in_array = 0;
        foreach ($data['fund_id_from'] as $key => $fund) {
            $total_money_in_array += $fund['money'];
        }

        if ($total_money_in_array != $total_money) {
            $result = [
                'unit' => 'Tổng tiền chi ra ở các quỹ không trùng với tổng tiền đổi'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Lấy ra FundTypePay nạp tiền TQ
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $fundThuT1 = $fundTypePayModel->where('code', 4)->first();

        if (is_null($fundThuT1)) {
            $result = [
                'unit' => 'Loại thanh toán Nạp tiền ngân hàng Trung Quốc ko có, liên hệ admin'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        try {
            \DB::beginTransaction();

            $fundService = \App::make('App\Services\FundService');

            // Tạo khoản nhận vào cho quỹ Trung
            $fund_to = $fundModel->where('id', $data['fund_id_to'])->first();
            $data_to = [
                'type_object'        => 3,

                'type_pay'           => 1,
                'fund_type_pay_id'   => $fundThuT1->id,
                'fund_type_pay_code' => $fundThuT1->code,

                'fund_id'            => $fund_to->id,

                'cn_change'          => $data['cn_change'],
                'cn_change_ratio'    => $data['cn_change_ratio'],
                'fee_change_cn'      => $data['fee_change_cn'],
                'fee_change_vnd'     => $data['fee_change_vnd'],

                'money'              => $data['cn_change'],

                'note'               => "Nạp tiền ngân hàng Trung Quốc",
            ];

            $result_to = $this->_service->store($data_to);

            // Tạo khoản chi ra cho từng quỹ Việt
            foreach ($data['fund_id_from'] as $key => $fund) {
                $data_from = [
                    'type_object'         => 3,

                    'type_pay'            => 2,
                    'fund_type_pay_id'    => $fundThuT1->id,
                    'fund_type_pay_code'  => $fundThuT1->code,

                    'fund_id'             => $fund['id'],

                    'fund_transaction_id' => $result_to->id,

                    'money'               => $fund['money'],
                    'note'                => "Nạp tiền vào quỹ " . $fund_to->name,
                ];

                $result_from = $this->_service->store($data_from);
            }

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }

    }

    public function saveMoneyReChargeGetCategory(Request $request)
    {
        $fundModel = \App::make('App\Models\Fund');
        $funds = $fundModel->all();
        $arrFundVN = [];
        $arrFundCN = [];
        if ($funds) {
            foreach ($funds as $key => $fund) {
                $temp = [
                    'id'              => $fund->id,
                    'name'            => $fund->name,
                    'current_balance' => $fund->current_balance,
                    'money'           => '',
                    'checked'         => false
                ];
                $temp2 = [
                    'id' => $fund->id,
                    'name' => $fund->name,
                ];
                if ($fund->unit_currency == 1 && $fund->current_balance) {
                    array_push($arrFundVN, $temp);
                }
                if ($fund->unit_currency == 2) {
                    array_push($arrFundCN, $temp2);
                }
            }
        }

        $data_result = [
            'arrFundVN' => $arrFundVN,
            'arrFundCN' => $arrFundCN,
        ];

        return resSuccessWithinData($data_result);
    }

    public function saveMoneyWithdrawal(Request $request)
    {
        $validationRules = [
            'customer_withdrawal_id' => 'required|exists:customer_withdrawal,id',
            'fund_id'        => 'required|exists:funds,id',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra yêu cầu rút tiền này là chờ xử lý mới được phép xử lý tiếp
        $customerWithdrawalModel = \App::make('App\Models\CustomerWithdrawal');
        $customerWithdrawalDB = $customerWithdrawalModel->where('id', $data['customer_withdrawal_id'])->first();

        $arr_done = [
            \App\Constants\CustomerConstant::KEY_WITHDRAWAL_STATUS_DONE,
            \App\Constants\CustomerConstant::KEY_WITHDRAWAL_STATUS_CANCEL,
        ];
        if (in_array($customerWithdrawalDB->status, $arr_done)) {
            $result = [
                'unit' => 'Yêu cầu này đã được xử lý'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra quỹ chi tiền phải là quỹ Việt Nam
        $fundModel = \App::make('App\Models\Fund');
        $fundDB = $fundModel->where('id', $data['fund_id'])
                                ->where('unit_currency', 1)
                                ->first();

        if (is_null($fundDB)) {
            $result = [
                'unit' => 'Quỹ chi tiền không phải quỹ VN, vui lòng kiểm tra lại'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Kiểm tra quỹ chi có đủ số dư để chi không
        if ($fundDB->current_balance < $customerWithdrawalDB->amount) {
            $result = [
                'unit' => 'Qũy không đủ số dư để thực hiện giao dịch này'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        // Lấy thông tin khách hàng
        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('id', $customerWithdrawalDB->customer_id)->first();

        // Lấy ra FundTypePay khách hàng rút tiền
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $fundChiT1 = $fundTypePayModel->where('code', 5)->first();

        if (is_null($fundChiT1)) {
            $result = [
                'unit' => 'Loại thanh toán khách hàng rút tiền ko có, liên hệ admin'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        try {
            \DB::beginTransaction();

            // Tạo bản ghi trừ tiền từ quỹ
            $dataChiQuy = [
                'type_object'        => 2,
                'customer_code'      => $customer->code,
                'type_pay'           => 2,
                'fund_type_pay_id'   => $fundChiT1->id,
                'fund_type_pay_code' => $fundChiT1->code,

                'customer_withdrawal_id' => $data['customer_withdrawal_id'],

                'fund_id'            => $data['fund_id'],
                'money'              => (int)$customerWithdrawalDB->amount,
                'note'               => "Thanh toán yêu cầu rút tiền " . $customerWithdrawalDB->code . " của khách hàng " . $customer->code,
            ];
            $result_1 = $this->_service->store($dataChiQuy);

            // Cập nhật trạng thái của yêu cầu rút tiền về hoàn thành
            $customerWithdrawalDB->status = \App\Constants\CustomerConstant::KEY_WITHDRAWAL_STATUS_DONE;
            $customerWithdrawalDB->save();

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }

    }

    public function listSupplierOrderDetails(Request $request)
    {
        $data = $request->all();
        $per_page = $request->get('per_page', 25);

        $orderModel = \App::make('App\Models\Order');

        $code = $request->code;
        $customer_code = $request->customer_code;
        $staff_order_id = $request->staff_order_id;

        $model  = $orderModel;

        if (!is_null($code)) {
            $model = $model->where('code', $code);
        }
        if (!is_null($staff_order_id)) {
            $model = $model->where('staff_order_id', $staff_order_id);
        }

        if (!is_null($customer_code)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_code)->orWhere('phone_number', $customer_code)->first();
            if ($customer) {
                $model = $model->where('customer_id', $customer->id);
            } else {
                $model = $model->where('customer_id', 0);
            }
        }

        $arr_status = [
            \App\Constants\OrderConstant::KEY_STATUS_WAIT_TO_PAY,
        ];
        $model = $model->whereIn('status', $arr_status);
        $model = $model->orderBy('created_at', 'desc');
        $orders = $model->paginate($per_page);

        $_paginateResource = \App\Http\Resources\PaginateJsonResource::class;
        $_resource = \App\Http\Resources\Order\OrderAccountantResource::class;
        return resSuccessWithinData(new $_paginateResource($orders, $_resource));
    }

    public function orderConfirmation(Request $request)
    {
        $validationRules = [
            'arr_order_id'   => 'required|array',
            'arr_order_id.*' => 'required|exists:orders,id',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra các đơn ở trạng thái chờ thanh toán thì mới dc cho đi tiieep
        $orderModel = \App::make('App\Models\Order');

        foreach ($data['arr_order_id'] as $key => $order_id) {
            $orderDB = $orderModel->where('id', $order_id)->first();
            if ($orderDB->status != \App\Constants\OrderConstant::KEY_STATUS_WAIT_TO_PAY) {
                $result = [
                    'unit' => 'Đơn hàng ' . $orderDB->code . ' không hợp lệ'
                ];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        // Xử lý
        try {
            \DB::beginTransaction();
            $logService = \App::make('App\Services\ActivityService');
            $userOnline = \Auth::user();
            foreach ($data['arr_order_id'] as $key => $order_id) {
                $orderDB = $orderModel->where('id', $order_id)->first();
                $orderDB->status = \App\Constants\OrderConstant::KEY_STATUS_ORDERED;
                $orderDB->save();

                $content = $userOnline->name . ' xác nhận đặt hàng cho đơn hàng: ' . $orderDB->code;
                $logService->setOrderLog($userOnline, $content, $order_id);
            }

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function orderError(Request $request)
    {
        $validationRules = [
            'order_id'  => 'required|exists:orders,id',
            'reason_id' => 'required|in:1,2',
            'note'      => 'nullable|max:255',
        ];
        $validationMessages = [
            'note.max' => 'Ghi chú không được quá 255 kí tự'
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra các đơn ở trạng thái chờ thanh toán thì mới dc cho đi tiieep
        $orderModel = \App::make('App\Models\Order');
        $orderDB = $orderModel->where('id', $data['order_id'])->first();
        if ($orderDB->status != \App\Constants\OrderConstant::KEY_STATUS_WAIT_TO_PAY) {
            $result = [
                'unit' => 'Đơn hàng ' . $orderDB->code . ' không hợp lệ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }
        // Xử lý
        try {
            \DB::beginTransaction();

            $userOnline = \Auth::user();
            $reson_text = '';
            if ($data['reason_id'] == 1) {
                $reson_text = 'Sai tiền';
            }
            if ($data['reason_id'] == 2) {
                $reson_text = 'Sai mã đặt hàng';
            }
            $content = $userOnline->name . ' tạo báo lỗi với lý do: ' . $reson_text;

            if ($data['note']) {
                $content .= ' (' . $data['note'] . ')';
            }

            // Chuyển trạng thái về đang đặt hàng
            $orderDB->status = \App\Constants\OrderConstant::KEY_STATUS_ORDERING;

            // Cập nhật error_log
            $error_logsDB = $orderDB->error_logs == null ? [] : array_reverse(json_decode($orderDB->error_logs));
            $temp_log = [
                'user_request_name'  => $userOnline->name,
                'user_request_email' => $userOnline->email,
                'user_request_id'    => $userOnline->id,

                'reson_text'         => $reson_text,
                'note'               => "",

                'time'               => date('Y-m-d H:i:s', time())
            ];
            if ($data['note']) {
                $temp_log['note'] = $data['note'];
            }
            array_push($error_logsDB, $temp_log);

            $orderDB->error_logs = json_encode($error_logsDB);

            // Lưu lại cho đơn

            $orderDB->save();

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function orderError06022023(Request $request)
    {
        $validationRules = [
            'order_id'  => 'required|exists:orders,id',
            'reason_id' => 'required|in:1,2',
            'note'      => 'nullable|max:255',
        ];
        $validationMessages = [
            'note.max' => 'Ghi chú không được quá 255 kí tự'
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        // Kiểm tra các đơn ở trạng thái chờ thanh toán thì mới dc cho đi tiieep
        $orderModel = \App::make('App\Models\Order');
        $orderDB = $orderModel->where('id', $data['order_id'])->first();
        if ($orderDB->status != \App\Constants\OrderConstant::KEY_STATUS_WAIT_TO_PAY) {
            $result = [
                'unit' => 'Đơn hàng ' . $orderDB->code . ' không hợp lệ'
            ];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }
        // Xử lý
        try {
            \DB::beginTransaction();

            $userOnline = \Auth::user();
            $reson_text = '';
            if ($data['reason_id'] == 1) {
                $reson_text = 'Sai tiền';
            }
            if ($data['reason_id'] == 2) {
                $reson_text = 'Sai mã đặt hàng';
            }
            $content = $userOnline->name . ' tạo báo lỗi với lý do: ' . $reson_text;

            if ($data['note']) {
                $content .= ' (' . $data['note'] . ')';
            }

            $logService = \App::make('App\Services\ActivityService');
            $logService->setOrderLog($userOnline, $content, $data['order_id']);
            dd('12321');
            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function fakeSave(Request $request)
    {
        try {
            \DB::beginTransaction();

            // Trừ tiền của quỹ
            $fundTypePayModel = \App::make('App\Models\FundTypePay');
            $fundTypeNapTien = $fundTypePayModel->where('code', 1)->first();


            $fundModel = \App::make('App\Models\Fund');
            $fund = $fundModel->where('unit_currency', 1)->first();

            $data_to = [
                'type_object'        => 2,
                'customer_code'      => null,
                'type_pay'           => 1,
                'fund_type_pay_id'   => $fundTypeNapTien->id,
                'fund_type_pay_code' => $fundTypeNapTien->code,

                'fund_id'            => $fund->id,

                'money'              => 1000000,

                'note'               => "Khách hàng nạp tiền banking",
            ];

            $result_to = $this->_service->store($data_to);

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }
}

