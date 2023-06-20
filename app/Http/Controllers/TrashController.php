<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;

class TrashController extends Controller
{
    public function __construct()
    {

    }

    public function search(Request $request)
    {
        $validationRules = [
            'date_to'   => 'nullable|date_format:"Y-m-d"',
            'date_from' => 'nullable|date_format:"Y-m-d"',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();
        $per_page = $request->get('per_page', 25);

        $customerModel = \App::make('App\Models\Customer');

        $date_from = $request->date_from;
        $date_to = $request->date_to;

        $model  = $customerModel;

        if (!is_null($date_from)) {
            $date_from .= ' 00:00:00';
            $model = $model->where('created_at', ">=", $date_from);
        }

        if (!is_null($date_to)) {
            $date_to .= ' 23:59:59';
            $model = $model->where('created_at', "<=", $date_to);
        }

        $model = $model->where('status', 1);
        $model = $model->orderBy('created_at', 'desc');
        $customers = $model->paginate($per_page);

        $_paginateResource = \App\Http\Resources\PaginateJsonResource::class;
        $_resource = \App\Http\Resources\Trash\CustomerResource::class;
        return resSuccessWithinData(new $_paginateResource($customers, $_resource));
    }

    public function searchOne(Request $request)
    {
        $validationRules = [
            'code'         => 'required',
        ];
        $validationMessages = [
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();

        $code = $request->code;

        $customerModel = \App::make('App\Models\Customer');

        $customer = $customerModel->where('code', $code)
                                ->orWhere('phone_number', $code)
                                ->where('status', 1)
                                ->first();
        // Nếu không nhập bất kì cái nào thì báo lỗi
        if (is_null($customer)) {
            $result = ['error' => 'Không tìm thấy khách hàng phù hợp'];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }

        $hasCustomer = false;
        $dataCustomer = [];

        if (!is_null($customer)) {
            $hasCustomer = true;
            $dataCustomer = [
                'id'           => $customer->id,
                'name'         => $customer->name,
                'code'         => $customer->code,
                'phone_number' => $customer->phone_number,
            ];
        }

        return resSuccessWithinData([
            'hasCustomer' => $hasCustomer,
            'dataCustomer' => $dataCustomer,
        ]);
    }

    public function clear(Request $request)
    {
        return resSuccess();
        $validationRules = [
            'arr_customer_id'      => 'required|array',
            'arr_customer_id.*'    => 'required|exists:customers,id',
            'clear_order'            => 'required|boolean',
            'clear_pack'             => 'required|boolean',
            'clear_all_keep_balance' => 'required|boolean',
            'clear_all'              => 'required|boolean',
        ];
        $validationMessages = [

        ];

        $validated = $request->validate($validationRules, $validationMessages);

        $data = $request->all();
        $data_origin = $request->all();

        if ($data['clear_all'] == true) {
            $data['clear_all_keep_balance'] = false;
            $data['clear_pack'] = false;
            $data['clear_order'] = false;
        }

        if ($data['clear_all_keep_balance'] == true) {
            $data['clear_pack'] = false;
            $data['clear_order'] = false;
        }

        // Check có ít nhất 1 cái phải = true
        $flag = false;

        if ($data['clear_all'] == true) {
            $flag = true;
        }
        if ($data['clear_all_keep_balance'] == true) {
            $flag = true;
        }
        if ($data['clear_pack'] == true) {
            $flag = true;
        }
        if ($data['clear_order'] == true) {
            $flag = true;
        }

        if ($flag == false) {
            $result = ['error' => 'Vui lòng chọn dữ liệu bạn muốn xóa'];
            return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
        }


        try {
            \DB::beginTransaction();

            // Lưu lịch sử
            $this->saveLog($data_origin);

            if ($data['clear_order']) {
                foreach ($data['arr_customer_id'] as $key => $customer_id) {
                    $this->clearOrder($customer_id);
                    $this->clearReportOrganizationOrders($customer_id);
                    $this->clearReportConsignments($customer_id);
                }
            }

            if ($data['clear_pack']) {
                foreach ($data['arr_customer_id'] as $key => $customer_id) {
                    $this->clearPack($customer_id);
                    $this->clearReportConsignments($customer_id);
                }
            }

            if ($data['clear_all_keep_balance']) {
                foreach ($data['arr_customer_id'] as $key => $customer_id) {
                    $this->clearOrder($customer_id, true);
                    $this->clearPack($customer_id);
                    $this->clearTransaction($customer_id);
                    $this->clearReportOrganizationOrders($customer_id);
                    $this->clearReportConsignments($customer_id);
                }
            }

            if ($data['clear_all']) {
                foreach ($data['arr_customer_id'] as $key => $customer_id) {
                    $this->clearOrder($customer_id, true);
                    $this->clearPack($customer_id);
                    $this->clearTransaction($customer_id);
                    $this->clearBalance($customer_id);
                    $this->clearCustomer($customer_id);
                    $this->clearReportOrganizationOrders($customer_id);
                    $this->clearReportConsignments($customer_id);
                }
            }

            // dd('1312321');

            \DB::commit();
            return resSuccess();
        } catch (Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function getHistory(Request $request)
    {
        $data = $request->all();
        $per_page = $request->get('per_page', 25);

        $activityModel = \App::make('App\Models\Activity');

        $code = $request->code;

        $model  = $activityModel;

        if (!is_null($code)) {
            $model = $model->where('code', $code);
        }

        $model = $model->where('log_name', 'trash_log');
        $model = $model->orderBy('created_at', 'desc');
        $activity = $model->paginate($per_page);

        $_paginateResource = \App\Http\Resources\PaginateJsonResource::class;
        $_resource = \App\Http\Resources\Activity\ActivityTrashResource::class;
        return resSuccessWithinData(new $_paginateResource($activity, $_resource));
    }

    public function saveLog($data_origin)
    {
        $customerModel = \App::make('App\Models\Customer');
        $customers = $customerModel->whereIn('id', $data_origin['arr_customer_id'])->get();

        $arr_customer = [];
        $arr_delete = [];

        if ($customers) {
            foreach ($customers as $key => $customer) {
                $temp = [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                ];
                array_push($arr_customer, $temp);
            }
        }

        if ($data_origin['clear_order']) {
            array_push($arr_delete, 'Xóa đơn hàng');
        }
        if ($data_origin['clear_pack']) {
            array_push($arr_delete, 'Xóa kiện hàng');
        }
        if ($data_origin['clear_all_keep_balance']) {
            array_push($arr_delete, 'Xóa tất cả để lại số dư');
        }
        if ($data_origin['clear_all']) {
            array_push($arr_delete, 'Xóa tất cả');
        }

        // Lưu xóa

        $activityModel = \App::make('App\Models\Activity');
        $userOnline = \Auth::user();
        $content = $userOnline->name . ' thực hiện xóa dữ liệu';
        $data = [
            'arr_customer' => $arr_customer,
            'arr_delete'   => $arr_delete,
        ];

        $data_log = [
            'causer_type' => get_class($userOnline),
            'causer_id' => $userOnline->id,
            'log_name' => 'trash_log',
            'content' => $content,
            'organization_id' => $userOnline->organization_id ?? getOrganization(),
            'properties' =>  json_encode($data),
        ];
        $activity = $activityModel->create($data_log);
    }

    public function clearReportOrganizationOrders($customer_id)
    {
        $reportOrganizationOrderModel = \App::make('App\Models\ReportOrganizationOrder');
        $modelDB =  $reportOrganizationOrderModel->first();

        if ($modelDB) {
            $select = '';
            foreach (array_keys(\App\Constants\OrderConstant::STATUSES) as $key => $value) {
                $select .= 'sum(if(orders.status = \''. $value .'\', 1, 0)) as total_' . $value . ',';
            }
            $select     = chop($select,",");

            $orderModel = \App::make('App\Models\Order');

            $result = $orderModel->selectRaw($select)->first();
            if ($result) {
                foreach (array_keys(\App\Constants\OrderConstant::STATUSES) as $key => $value) {
                    $modelDB->{$value} = (int)$result->{'total_' . $value};
                }
                $modelDB->save();
            }
        }
    }

    public function clearReportConsignments($customer_id)
    {
        $reportConsignmentModel = \App::make('App\Models\ReportConsignment');
        $modelDB =  $reportConsignmentModel->where('customer_id', $customer_id)->first();

        if ($modelDB) {
            foreach (array_keys(\App\Constants\ConsignmentConstant::STATUSES) as $key => $value) {
                $modelDB->{$value} = 0;
            }
            $modelDB->save();
        }
    }

    public function clearOrder($customer_id, $all = false)
    {
        $orderModel = \App::make('App\Models\Order');
        $orders = $orderModel->where('customer_id', $customer_id)
                            ->where('packages_number', '=', 0)
                            ->get();

        if ($all) {
            $orders = $orderModel->where('customer_id', $customer_id)->get();
        }

        if ($orders) {
            $orderSupplierModel = \App::make('App\Models\OrderSupplier');
            $orderStatusTimeModel = \App::make('App\Models\OrderStatusTime');
            $orderNoteModel = \App::make('App\Models\OrderNote');
            $orderDetailModel = \App::make('App\Models\OrderDetail');
            $deliveryOrderModel = \App::make('App\Models\DeliveryOrder');
            $reportOrderVNModel = \App::make('App\Models\ReportOrderVN');
            $complainModel = \App::make('App\Models\Complain');

            foreach ($orders as $key => $order) {
                $order_suppliers = $orderSupplierModel->where('order_id', $order->id)->delete();
                $order_status_times = $orderStatusTimeModel->where('order_id', $order->id)->delete();
                $order_notes = $orderNoteModel->where('order_id', $order->id)->delete();
                $order_details = $orderDetailModel->where('order_id', $order->id)->delete();
                $delivery_orders = $deliveryOrderModel->where('order_id', $order->id)->delete();
                $report_orders_vn = $reportOrderVNModel->where('order_id', $order->id)->delete();
                $complains = $complainModel->where('order_id', $order->id)->delete();

                $order->delete();
            }
        }

        // Cần check lại chỗ này withoutGlobalScopes
        $consignmentModel = \App::make('App\Models\Consignment');
        $consignments = $consignmentModel->where('customer_id', $customer_id)->withoutGlobalScopes()->get();

        if ($consignments) {
            $consignmentDetailModel = \App::make('App\Models\ConsignmentDetail');
            $consignmentStatusTimeModel = \App::make('App\Models\ConsignmentStatusTime');

            foreach ($consignments as $key => $consignment) {
                $consignment_status_times = $consignmentStatusTimeModel->where('consignment_id', $consignment->id)->delete();
                $consignment_details = $consignmentDetailModel->where('consignment_id', $consignment->id)->delete();
                $consignment->delete();
            }
        }

        // Cập nhật số lượng đơn
        $orders_number = $orderModel->where('customer_id', $customer_id)->count();
        $reportCustomersModel = \App::make('App\Models\ReportCustomer');
        $report_customer = $reportCustomersModel->where('customer_id', $customer_id)->first();

        $report_customer->orders_number = $orders_number;
        $report_customer->consignment_number = 0;
        $report_customer->save();
    }

    public function clearPack($customer_id)
    {
        $orderPackageModel = \App::make('App\Models\OrderPackage');
        $order_packages = $orderPackageModel->where('customer_id', $customer_id)->get();

        if ($order_packages) {
            $orderDetailPackageModel = \App::make('App\Models\OrderDetailPackage');
            $orderPackageImageModel = \App::make('App\Models\OrderPackageImage');
            $orderPackageNoteModel = \App::make('App\Models\OrderPackageNote');
            $orderPackageStatusTimeModel = \App::make('App\Models\OrderPackageStatusTime');
            $orderPackageStatusTimeModel = \App::make('App\Models\OrderPackageStatusTime');
            $reportPackageModel = \App::make('App\Models\ReportPackage');
            $consignmentModel = \App::make('App\Models\Consignment');

            foreach ($order_packages as $key => $order_package) {
                $order_detail_packages = $orderDetailPackageModel->where('order_package_id', $order_package->id)->delete();
                $order_package_images = $orderPackageImageModel->where('order_package_id', $order_package->id)->delete();
                $order_package_notes = $orderPackageNoteModel->where('order_package_id', $order_package->id)->delete();
                $order_package_status_times = $orderPackageStatusTimeModel->where('order_package_id', $order_package->id)->delete();
                $report_packages = $reportPackageModel->where('customer_id', $customer_id)->delete();

                // Nếu kiện bên kí gửi thì xóa hết đơn kí gửi
                if ($order_package->order_type == 'App\Models\Consignment') {
                    // Cần check lại chỗ này withoutGlobalScopes
                    $orderDB = $consignmentModel->where('id', $order_package->order_id)->withoutGlobalScopes()->first();
                    if ($orderDB) {
                        $orderDB->delete();
                    }
                }

                // Nếu kiện order thì cập nhật lại số lượng bên đơn
                if ($order_package->order_type == 'App\Models\Order') {
                    $orderDB = $order_package->order;
                    if ($orderDB) {
                        $orderDB->packages_number = 0;
                        $orderDB->save();
                    }
                }
                $order_package->delete();
            }

            // Cập nhật số lượng
            $orderModel = \App::make('App\Models\Order');
            $orders_number = $orderModel->where('customer_id', $customer_id)->count();
            $reportCustomersModel = \App::make('App\Models\ReportCustomer');
            $report_customer = $reportCustomersModel->where('customer_id', $customer_id)->first();

            $report_customer->orders_number = $orders_number;
            $report_customer->packages_number = 0;
            $report_customer->consignment_number = 0;
            $report_customer->save();
        }
    }

    public function clearTransaction($customer_id)
    {
        $transactionModel = \App::make('App\Models\Transaction');
        $transactions = $transactionModel->where('customer_id', $customer_id)->delete();
    }

    public function clearBalance($customer_id)
    {
        $reportCustomersModel = \App::make('App\Models\ReportCustomer');
        $report_customer = $reportCustomersModel->where('customer_id', $customer_id)->first();

        $report_customer->balance_amount = 0;
        $report_customer->save();
    }

    public function clearCustomer($customer_id)
    {
        $customerModel = \App::make('App\Models\Customer');
        $customer = $customerModel->where('id', $customer_id)->delete();
    }

}

