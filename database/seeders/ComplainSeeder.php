<?php

namespace Database\Seeders;

use App\Constants\ComplainConstant;
use App\Helpers\DatabaseHelper;
use App\Models\Complain;
use App\Models\ComplainFeedback;
use App\Models\ComplainStatusTime;
use App\Models\ComplainType;
use App\Models\Organization;
use App\Models\ReportComplain;
use App\Models\Solution;
use App\Services\CustomerService;
use Illuminate\Database\Seeder;

class ComplainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $solution = ['Nhận bồi thường', 'Đổi trả', 'Phát bù'];
        $complainType = [
            'Đặt hàng chậm',
            'Thái độ phục vụ của nhân viên',
            'Trừ tiền ví điện tử chưa chính xác',
            'Đơn hàng sai lệch về tiền',
            'Tính sai cước cân nặng',
            'Ship Trung Quốc cao',
            'Hàng về chậm',
            'Hàng sai mẫu mã, quy cách',
            'Hàng vỡ hỏng',
            'Hàng thiếu',
        ];
        Complain::query()->truncate();
        ComplainType::query()->truncate();
        ComplainFeedback::query()->truncate();
        Solution::query()->truncate();
        ReportComplain::query()->truncate();
        ComplainType::query()->insert(DatabaseHelper::getData($complainType, true));
        Solution::query()->insert(DatabaseHelper::getData($solution));
        Complain::factory(1)->create()->transform(function($complain) {
            ComplainStatusTime::query()->create(['complain_id' => $complain->id, 'key' => ComplainConstant::KEY_STATUS_PENDING]);
        });
        ReportComplain::query()->create(
            [
                'organization_id' => Organization::query()->first()->id,
                'customer_id' => (new CustomerService())->getCustomerTest()->id
            ]
        );
    }


}
