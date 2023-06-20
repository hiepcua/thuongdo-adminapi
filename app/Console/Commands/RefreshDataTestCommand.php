<?php

namespace App\Console\Commands;

use App\Constants\ComplainConstant;
use App\Constants\ConsignmentConstant;
use App\Constants\FineConstant;
use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Models\Activity;
use App\Models\BaseModel;
use App\Models\Complain;
use App\Models\ComplainFeedback;
use App\Models\ComplainFeedbackAttachment;
use App\Models\ComplainImage;
use App\Models\ComplainStatusTime;
use App\Models\Consignment;
use App\Models\ConsignmentDetail;
use App\Models\ConsignmentStatusTime;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Fine;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailImage;
use App\Models\OrderPackage;
use App\Models\OrderSupplier;
use App\Models\ReportComplain;
use App\Models\ReportConsignment;
use App\Models\ReportCustomer;
use App\Models\ReportFine;
use App\Models\ReportLevel;
use App\Models\ReportOrderVN;
use App\Models\ReportOrganizationOrder;
use App\Models\ReportPackage;
use App\Models\ReportRevenue;
use App\Models\ReportUserCounselingCustomer;
use App\Models\ReportUserQuotationCustomer;
use App\Models\ReportUserTakeCareCustomer;
use App\Models\Transaction;
use Illuminate\Console\Command;

class RefreshDataTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa dữ liệu test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Order::query()->truncate();
        OrderDetail::query()->truncate();
        Consignment::query()->truncate();
        ConsignmentDetail::query()->truncate();
        ConsignmentStatusTime::query()->truncate();
        ReportOrderVN::query()->truncate();
        OrderPackage::query()->truncate();
        OrderDetailImage::query()->truncate();
        Complain::query()->truncate();
        ComplainStatusTime::query()->truncate();
        ComplainFeedback::query()->truncate();
        ComplainFeedbackAttachment::query()->truncate();
        ComplainImage::query()->truncate();
        Transaction::query()->truncate();
        Activity::query()->truncate();
        ReportUserCounselingCustomer::query()->truncate();
        ReportUserQuotationCustomer::query()->truncate();
        ReportUserTakeCareCustomer::query()->truncate();
        OrderSupplier::query()->truncate();
        Fine::query()->truncate();
        DeliveryOrder::query()->truncate();
        ReportRevenue::query()->truncate();
        foreach (Customer::query()->cursor() as $customer) {
            $customer->level = 0;
            $customer->save();
        }
        $this->refresh(
            new ReportCustomer(),
            array_merge(
                [
                    'orders_number',
                    'order_amount',
                    'consignment_number',
                    'packages_received_number',
                    'packages_number',
                    'deposited_amount',
                    'withdrawal_amount',
                    'purchase_amount',
                    'discount_amount'
                ],
                array_keys(OrderConstant::STATUSES)
            )
        );
        $this->refresh(new ReportComplain(), array_keys(ComplainConstant::STATUSES));
        $this->refresh(new ReportConsignment(), array_keys(ConsignmentConstant::STATUSES));
        $this->refresh(new ReportLevel(), ['quantity']);
        $this->refresh(new ReportPackage(), array_keys(PackageConstant::STATUSES));
        $this->refresh(new ReportOrganizationOrder(), array_keys(OrderConstant::STATUSES));
        $this->refresh(new ReportFine(), array_keys(FineConstant::STATUSES));
        return 0;
    }

    private function refresh(BaseModel $model, array $array)
    {
        foreach ($model::query()->cursor() as $item) {
            foreach ($array as $value) {
                $item->{$value} = 0;
            }
            $item->save();
        }
    }
}
