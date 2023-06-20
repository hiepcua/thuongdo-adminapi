<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\FundTypePayService;

class FundTypePayController extends Controller
{
    public function __construct(FundTypePayService $service)
    {
        $this->_service = $service;
    }

    public function storeMessage(): ?array
    {
       return [
        'type.required' => 'Kiểu thu chi bắt buộc phải nhập',
        'name.required' => 'Tên thu chi bắt buộc phải nhập',
       ];
    }

    public function storeRequest(): array
    {
        return [
            'type' => 'required|in:1,2',
            'name' => 'required|max:191',
        ];
    }

    public function updateMessage(): array
    {
        return [
            'type.required' => 'Kiểu thu chi bắt buộc phải nhập',
            'name.required' => 'Tên thu chi bắt buộc phải nhập',
        ];
    }

    public function updateRequest(string $id): array
    {
        return [
            'type' => 'required|in:1,2',
            'name' => 'required|max:191',
        ];
    }

    public function update(string $id): JsonResponse
    {
        $this->throwValidationAndAction(__FUNCTION__, $id);
        $data = request()->only([
            'name'
        ]);
        return resSuccessWithinData($this->_service->update($id, $data));
    }

    public function updateStatus(Request $request, $id)
    {
        $data = request()->all();
        $fundTypePayModel = \App::make('App\Models\FundTypePay');
        $fundTypePay = $fundTypePayModel->where('id', $id)->first();
        if (is_null($fundTypePay)) {
            return resError('Bản ghi không tồn tại');
        }
        $fundTypePay->status = $data['status'];
        $fundTypePay->save();
        return resSuccess();
    }

    public function initDefault(Request $request)
    {
        $fundTypePayModel = \App::make('App\Models\FundTypePay');

        $data2 = [
            ['name' => 'Khách hàng nạp tiền', 'type' => 1, 'code' => 1],
            ['name' => 'Đổi tiền', 'type' => 0, 'code' => 2],
            ['name' => 'Chuyển quỹ', 'type' => 0, 'code' => 3], // Cả thu và cả chi
            ['name' => 'Nạp tiền Trung Quốc', 'type' => 0, 'code' => 4], // Cả thu và cả chi
            ['name' => 'Khách hàng rút tiền', 'type' => 0, 'code' => 5],

            ['name' => 'Thuê văn phòng', 'type' => 2, 'code' => 0],
            ['name' => 'Chi phí Marketing', 'type' => 2, 'code' => 0],

            ['name' => 'Hoàn tiền', 'type' => 0, 'code' => 6], // Hoàn tiền trường hợp xóa
            ['name' => 'Cập nhật số dư', 'type' => 0, 'code' => 7], // Hoàn tiền trường hợp xóa
        ];

        // $data2 = [
        //     ['name' => 'Khách hàng nạp tiền', 'type' => 1, 'code' => 1],
        //     ['name' => 'Đổi tiền', 'type' => 2, 'code' => 2],
        //     ['name' => 'Chuyển quỹ', 'type' => 0, 'code' => 3], // Cả thu và cả chi
        //     ['name' => 'Nạp tiền Trung Quốc', 'type' => 0, 'code' => 4], // Cả thu và cả chi
        //     ['name' => 'Khách hàng rút tiền', 'type' => 2, 'code' => 5],

        //     ['name' => 'Thuê văn phòng', 'type' => 2, 'code' => 0],
        //     ['name' => 'Chi phí Marketing', 'type' => 2, 'code' => 0],

        //     ['name' => 'Hoàn tiền', 'type' => 0, 'code' => 6], // Hoàn tiền trường hợp xóa
        // ];

        $userOnline = \Auth::user();

        foreach ($data2 as $key => $item) {
            $fundTypePayDB = $fundTypePayModel->where('name', $item['name'])
                                    ->first();

            $item['organization_id'] = $userOnline->organization_id;
            $item['status'] = 1;

            if (is_null($fundTypePayDB)) {
                $fundTypePayModel->create($item);
            }
        }
        return resSuccess();
    }

}

