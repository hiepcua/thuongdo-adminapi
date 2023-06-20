<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Constants\ComplainConstant;

class StatisticController extends Controller
{
    public function fast(Request $request)
    {
        $time = $request->get('time', 'd');
        $user_type = $request->get('user_type', 0);
        $customer_search = $request->get('customer_search', null);

        if ($user_type == 0) {
            return resSuccess('Done');
        }

        // CEO = 1
        // Trưởng Phòng đặt hàng = 2
        // NV Báo giá = 3
        // NV Đặt hàng = 4
        // TP Tư vấn = 5
        // NV Tư vấn = 6
        // TP Chăm sóc = 7
        // NV Chăm sóc = 8
        // Kế toán = 9
        // Marketting = 10
        // Kho Trung Quốc = 11
        // Kho Trung Chuyển = 12
        // Kho Chi nhánh = 13

        if ($user_type == 2) {
            return $this->getFastType2($time, $customer_search);
        }
        if ($user_type == 3) {
            return $this->getFastType3($time, $customer_search);
        }
        if ($user_type == 4) {
            return $this->getFastType4($time, $customer_search);
        }
        if ($user_type == 5) {
            return $this->getFastType5($time, $customer_search);
        }
        if ($user_type == 6) {
            return $this->getFastType6($time, $customer_search);
        }
        if ($user_type == 7) {
            return $this->getFastType7($time, $customer_search);
        }
        if ($user_type == 8) {
            return $this->getFastType8($time, $customer_search);
        }
        return resSuccess('OK');
    }

    public function getFastType2($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $complainModel = \App::make('App\Models\Complain');

        $orderM1  = $orderModel;
        $orderM2  = $orderModel;
        $orderM3  = $orderModel;
        $complainM  = $complainModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $orderM2 = $orderM2->where('customer_id', $customer->id);
                $orderM3 = $orderM3->where('customer_id', $customer->id);
                $complainM = $complainM->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_current = [
            'total_all_order' => 0, // Tổng đơn
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Đã báo giá
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
        ];
        $arr_previous = [
            'total_all_order' => 0, // Tổng đơn
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Đã báo giá
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_order,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_current['total_all_order']       = $result_1->total_all_order;
            $arr_current['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_current['total_waiting_deposit'] = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_current['total_ordered']         = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
            $arr_current['total_done']            = $result_1->total_done == null ? 0 : (int)$result_1->total_done;
        }

        $result_2 = $orderM2->whereBetween('updated_at', [$dateRange['startPrevious'], $dateRange['endPrevious']])
                            ->selectRaw($select)->first();

        if ($result_2) {
            $arr_previous['total_all_order']       = $result_2->total_all_order;
            $arr_previous['total_waiting_quote']   = $result_2->total_waiting_quote == null ? 0 : (int)$result_2->total_waiting_quote;
            $arr_previous['total_waiting_deposit'] = $result_2->total_waiting_deposit == null ? 0 : (int)$result_2->total_waiting_deposit;
            $arr_previous['total_ordered']         = $result_2->total_ordered == null ? 0 : (int)$result_2->total_ordered;
            $arr_previous['total_done']            = $result_2->total_done == null ? 0 : (int)$result_2->total_done;
        }

        // Khiếu nại

        $arr_complain = [
            'total_pending' => 0, // Tổng khiếu nại chờ xử lý
            'total_process' => 0, // Tổng khiếu nại đang xử lý
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_complain,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PENDING .'\', 1, 0)) as total_pending,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESS .'\', 1, 0)) as total_process,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESSED .'\', 1, 0)) as total_done';
        $result_3 = $complainM->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();
        if ($result_3) {
            $arr_complain['total_pending'] = $result_3->total_pending == null ? 0 : (int)$result_3->total_pending;
            $arr_complain['total_process'] = $result_3->total_process == null ? 0 : (int)$result_3->total_process;
            $arr_complain['total_done']    = $result_3->total_done == null ? 0 : (int)$result_3->total_done;
        }

        // Đơn quá ngày:
        // - Tổng đơn chưa hoàn thành quá ngày: 3 ngày tính từ thời điểm kiện hàng cuối cùng trong đơn về VN
        // - Tổng kiện chưa lấy hàng quá ngày: 3 ngày các kiện hàng ở trạng thái đến kho VN
        // - Tổng đơn chờ thanh lý: 1 tháng ở trạng thái đến kho VN

        $arr_outdate = [
            'waiting_for_liquidation' => 0, // Chờ thanh lý
            'not_take_pack' => 0, // Chưa lấy hàng quá ngày
            'not_done' => 0, // Chưa hoàn thành quá ngày
        ];

        $orderOutDate  = $orderM3->where('status', OrderConstant::KEY_STATUS_ORDERED)
                                    ->where('packages_number', '>', 0)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        // dd(\Arr::pluck($orderOutDate, 'code'));
        if ($orderOutDate) {
            $orderPackageStatusTimeModel = \App::make('App\Models\OrderPackageStatusTime');

            foreach ($orderOutDate as $key => $order) {
                $order_packages_ids = \Arr::pluck($order->packages, 'id');
                // dump($order_packages_ids);
                $count_waiting_for_liquidation = 0; // Chờ thanh lý
                $count_not_take_pack = 0; // Chưa lấy hàng quá ngày
                $count_not_done = 0; // Chưa hoàn thành quá ngày

                foreach ($order_packages_ids as $key => $order_packages_id) {
                    $last_order_packages_status_time = $orderPackageStatusTimeModel->where('order_package_id', $order_packages_id)
                                                                        ->orderBy('created_at', 'desc')
                                                                        ->first();
                    // dump($last_order_packages_status_time->key);
                    if ($last_order_packages_status_time) {
                        // Chờ thanh lý
                        // Chưa lấy hàng quá ngày
                        if ($last_order_packages_status_time->key == PackageConstant::STATUS_WAREHOUSE_VN) {
                            $timeNow = time();;
                            $timeStatus = strtotime($last_order_packages_status_time->created_at);
                            $diffTime = (int)round(abs($timeNow - $timeStatus) / 60);

                            if ($diffTime > 15) {
                                $count_waiting_for_liquidation += 1;
                            }
                            if ($diffTime > 15) {
                                $count_not_take_pack += 1;
                            }
                        }

                        // Chưa hoàn thành quá ngày
                        $arr_status_pack_to_vn = [
                            PackageConstant::STATUS_WAREHOUSE_VN,
                            PackageConstant::STATUS_ON_THE_WAY_HCM,
                            PackageConstant::STATUS_CHECKING_GOODS_HCM,
                            PackageConstant::STATUS_ON_THE_WAY_HP,
                        ];
                        $in = in_array($last_order_packages_status_time->key, $arr_status_pack_to_vn);
                        if ($in) {
                            $timeNow = time();;
                            $timeStatus = strtotime($last_order_packages_status_time->created_at);
                            $diffTime = (int)round(abs($timeNow - $timeStatus) / 60);
                            if ($diffTime > 15) {
                                $count_not_done += 1;
                            }
                        }
                    }
                }

                // Cộng trừ
                if ($count_waiting_for_liquidation == count($order_packages_ids)) {
                    $arr_outdate['waiting_for_liquidation'] += 1;
                }
                $arr_outdate['not_take_pack'] += $count_not_take_pack;
                if ($count_not_done == count($order_packages_ids)) {
                    $arr_outdate['not_done'] += 1;
                }
            }
        }


        $data_result = [
            'arr_current'  => $arr_current,
            'arr_previous' => $arr_previous,
            'arr_outdate'  => $arr_outdate,
            'arr_complain' => $arr_complain,
        ];
        return resSuccessWithinData($data_result);
    }

    public function getFastType3($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');

        $orderM1  = $orderModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_current = [
            'total_full' => 0, // Tổng doanh số
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Đã báo giá
            'total_done' => 0, // Hoàn thành
        ];

        // orders: order_cost, order_fee, inspection_cost, china_shipping_cost
        // order_package: insurance_cost, woodworking_cost, international_shipping_cost, shock_proof_cost, storage_cost, delivery_cost

        $select = 'sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', order_cost + order_fee + inspection_cost + china_shipping_cost * exchange_rate, 0)) as total_full,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        // $result_2 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
        //                     ->where('status', OrderConstant::KEY_STATUS_DONE)
        //                     ->get();
        // dd(\Arr::pluck($result_2, 'code'));

        if ($result_1) {
            $arr_current['total_full']       = $result_1->total_full == null ? 0 : (int)$result_1->total_full;
            $arr_current['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_current['total_waiting_deposit'] = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_current['total_done']            = $result_1->total_done == null ? 0 : (int)$result_1->total_done;
        }

        // Xử lý đếm bên kiện
        $order_packageDB = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                                    ->where('status', OrderConstant::KEY_STATUS_DONE)
                                    ->get();

        if ($order_packageDB) {
            $order_ids = \Arr::pluck($order_packageDB, 'id');
            // dd($order_ids);
            $orderPackageModel = \App::make('App\Models\OrderPackage');

            $select_package = 'sum(insurance_cost + woodworking_cost + international_shipping_cost + shock_proof_cost + storage_cost + delivery_cost) as total_full_in_package';

            $result_2 = $orderPackageModel->whereIn('order_id', $order_ids)->selectRaw($select_package)->first();
            if ($result_2) {
                $arr_current['total_full'] += $result_2->total_full_in_package == null ? 0 : (int)$result_2->total_full_in_package;
            }
        }

        $data_result = [
            'arr_current'  => $arr_current
        ];
        return resSuccessWithinData($data_result);
    }

    public function getFastType4($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $orderPackageModel = \App::make('App\Models\OrderPackage');

        $orderM1  = $orderModel;
        $orderM2  = $orderModel;
        $orderPackageDB = $orderPackageModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $orderM2 = $orderM2->where('customer_id', $customer->id);
                $orderPackageDB = $orderPackageDB->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_current = [
            'total_deposited' => 0, // Chờ đặt hàng
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
            'total_not_done' => 0,  // Chưa hoàn thành (tất cả các đơn trừ tt đã hoàn thành + đã hủy ạ)
            'total_full' => 0, // Tổng doanh số,
            'total_pack_not_take' => 0, // Tồng kiện chưa lấy hàng
        ];
        $arr_previous = [
            'total_deposited' => 0, // Chờ đặt hàng
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
            'total_not_done' => 0,  // Chưa hoàn thành (tất cả các đơn trừ tt đã hoàn thành + đã hủy ạ)
            'total_full' => 0, // Tổng doanh số
        ];

        $select = 'sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', order_cost + order_fee + inspection_cost + china_shipping_cost * exchange_rate, 0)) as total_full,
                   sum(if(orders.status not in ( \''. OrderConstant::KEY_STATUS_DONE .'\', \''. OrderConstant::KEY_STATUS_CANCEL .'\'), 1, 0)) as total_not_done,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DEPOSITED .'\', 1, 0)) as total_deposited,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_current['total_deposited']   = $result_1->total_deposited == null ? 0 : (int)$result_1->total_deposited;
            $arr_current['total_ordered']   = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
            $arr_current['total_done']   = $result_1->total_done == null ? 0 : (int)$result_1->total_done;
            $arr_current['total_not_done']   = $result_1->total_not_done == null ? 0 : (int)$result_1->total_not_done;
            $arr_current['total_full']   = $result_1->total_full == null ? 0 : (int)$result_1->total_full;
        }

        $result_2 = $orderM2->whereBetween('updated_at', [$dateRange['startPrevious'], $dateRange['endPrevious']])
                            ->selectRaw($select)->first();

        if ($result_2) {
            $arr_previous['total_deposited']   = $result_2->total_deposited == null ? 0 : (int)$result_2->total_deposited;
            $arr_previous['total_ordered']   = $result_2->total_ordered == null ? 0 : (int)$result_2->total_ordered;
            $arr_previous['total_done']   = $result_2->total_done == null ? 0 : (int)$result_2->total_done;
            $arr_previous['total_not_done']   = $result_2->total_not_done == null ? 0 : (int)$result_2->total_not_done;
            $arr_previous['total_full']   = $result_2->total_full == null ? 0 : (int)$result_2->total_full;
        }

        // Xử lý đếm bên kiện
        $order_packageDB = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                                    ->where('status', OrderConstant::KEY_STATUS_DONE)
                                    ->get();

        if ($order_packageDB) {
            $order_ids = \Arr::pluck($order_packageDB, 'id');
            // dd($order_ids);
            $orderPackageModel = \App::make('App\Models\OrderPackage');

            $select_package = 'sum(insurance_cost + woodworking_cost + international_shipping_cost + shock_proof_cost + storage_cost + delivery_cost) as total_full_in_package';

            $result_2 = $orderPackageModel->whereIn('order_id', $order_ids)->selectRaw($select_package)->first();
            if ($result_2) {
                $arr_current['total_full'] += $result_2->total_full_in_package == null ? 0 : (int)$result_2->total_full_in_package;
            }
        }

        // Tổng kiện chưa lấy hàng
        // kiện ở tt Đến kho vn a
        $orderPackageDB = $orderPackageDB->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                                            ->where('status', PackageConstant::STATUS_WAREHOUSE_VN);

        $select = 'count(*) as total_pack_not_take';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->first();

        if ($orderPackageV1) {
            $arr_current['total_pack_not_take']   = $orderPackageV1->total_pack_not_take == null ? 0 : (int)$orderPackageV1->total_pack_not_take;
        }

        $data_result = [
            'arr_current'  => $arr_current,
            'arr_previous' => $arr_previous,
        ];
        return resSuccessWithinData($data_result);
    }

    public function getFastType5($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $complainModel = \App::make('App\Models\Complain');
        $orderPackageModel = \App::make('App\Models\OrderPackage');

        $orderM1  = $orderModel;
        $complainM  = $complainModel;
        $orderPackageDB = $orderPackageModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $complainM = $complainM->where('customer_id', $customer->id);
                $orderPackageDB = $orderPackageDB->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_order = [
            'total_not_done' => 0,  // Chưa hoàn thành (tất cả các đơn trừ tt đã hoàn thành + đã hủy ạ)
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Chờ đặt cọc
            'total_ordered' => 0, // Đã đặt hàng
        ];

        $select = 'sum(if(orders.status not in ( \''. OrderConstant::KEY_STATUS_DONE .'\', \''. OrderConstant::KEY_STATUS_CANCEL .'\'), 1, 0)) as total_not_done,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_order['total_not_done']   = $result_1->total_not_done == null ? 0 : (int)$result_1->total_not_done;
            $arr_order['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_order['total_waiting_deposit']   = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_order['total_ordered']   = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
        }

        // Kiện
        $arr_pack = [
            'total_pack_not_take' => 0, // Tổng kiện hàng chưa lấy (kiện ở tt Đến kho vn a)
        ];

        $orderPackageDB = $orderPackageDB->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                                            ->where('status', PackageConstant::STATUS_WAREHOUSE_VN);

        $select = 'count(*) as total_pack_not_take';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->first();

        if ($orderPackageV1) {
            $arr_pack['total_pack_not_take']   = $orderPackageV1->total_pack_not_take == null ? 0 : (int)$orderPackageV1->total_pack_not_take;
        }

        // Khiếu nại
        $arr_complain = [
            'total_pending' => 0, // Tổng khiếu nại chưa xử lý
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_complain,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PENDING .'\', 1, 0)) as total_pending,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESSED .'\', 1, 0)) as total_done';
        $result_3 = $complainM->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();
        if ($result_3) {
            $arr_complain['total_pending'] = $result_3->total_pending == null ? 0 : (int)$result_3->total_pending;
            $arr_complain['total_done']    = $result_3->total_done == null ? 0 : (int)$result_3->total_done;
        }

        // Khách hàng
        $arr_customer = [
            'total_waiting_for_advice' => 0, // Khách hàng chờ tư vấn
            'total_new' => 0, // Khách hàng mới
            'total_order' => 0, // Khách hàng order
            'total_consignment' => 0, // Khách hàng kí gửi
        ];
        $customerModel = \App::make('App\Models\Customer');

        $select = 'count(*) as total_new,
                   sum(if(staff_counselor_id is null and staff_care_id is null, 1, 0)) as total_waiting_for_advice,
                   sum(if(customers.service = 0, 1, 0)) as total_order,
                   sum(if(customers.service = 1, 1, 0)) as total_consignment';

        $result_4 = $customerModel->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_4) {
            $arr_customer['total_waiting_for_advice'] = $result_4->total_waiting_for_advice == null ? 0 : (int)$result_4->total_waiting_for_advice;
            $arr_customer['total_new']    = $result_4->total_new == null ? 0 : (int)$result_4->total_new;
            $arr_customer['total_order']    = $result_4->total_order == null ? 0 : (int)$result_4->total_order;
            $arr_customer['total_consignment']    = $result_4->total_consignment == null ? 0 : (int)$result_4->total_consignment;
        }

        $data_result = [
            'arr_order'    => $arr_order,
            'arr_pack'     => $arr_pack,
            'arr_complain' => $arr_complain,
            'arr_customer' => $arr_customer,
        ];
        return resSuccessWithinData($data_result);
    }
    public function getFastType6($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $complainModel = \App::make('App\Models\Complain');
        $orderPackageModel = \App::make('App\Models\OrderPackage');

        $orderM1  = $orderModel;
        $complainM  = $complainModel;
        $orderPackageDB = $orderPackageModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $complainM = $complainM->where('customer_id', $customer->id);
                $orderPackageDB = $orderPackageDB->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_order = [
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Chờ đặt cọc
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
            'total_order_fee' => 0, // Tổng doanh số dịch vụ (phí đặt hàng của đơn hoàn thành)
        ];

        $select = 'sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', order_fee, 0)) as total_order_fee,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_order['total_done']   = $result_1->total_done == null ? 0 : (int)$result_1->total_done;
            $arr_order['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_order['total_waiting_deposit']   = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_order['total_ordered']   = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
            $arr_order['total_order_fee']   = $result_1->total_order_fee == null ? 0 : (int)$result_1->total_order_fee;
        }

        // Kiện
        $arr_pack = [
            'total_pack_not_take' => 0, // Tổng kiện hàng chưa lấy (kiện ở tt Đến kho vn a)
        ];

        $orderPackageDB = $orderPackageDB->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                                            ->where('status', PackageConstant::STATUS_WAREHOUSE_VN);

        $select = 'count(*) as total_pack_not_take';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->first();

        if ($orderPackageV1) {
            $arr_pack['total_pack_not_take']   = $orderPackageV1->total_pack_not_take == null ? 0 : (int)$orderPackageV1->total_pack_not_take;
        }

        // Khiếu nại
        $arr_complain = [
            'total_pending' => 0, // Tổng khiếu nại chờ xử lý
            'total_process' => 0, // Tổng khiếu nại đang xử lý
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_complain,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PENDING .'\', 1, 0)) as total_pending,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESS .'\', 1, 0)) as total_process,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESSED .'\', 1, 0)) as total_done';
        $result_3 = $complainM->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();
        if ($result_3) {
            $arr_complain['total_pending'] = $result_3->total_pending == null ? 0 : (int)$result_3->total_pending;
            $arr_complain['total_process'] = $result_3->total_process == null ? 0 : (int)$result_3->total_process;
            $arr_complain['total_done']    = $result_3->total_done == null ? 0 : (int)$result_3->total_done;
        }


        $data_result = [
            'arr_order'    => $arr_order,
            'arr_pack'     => $arr_pack,
            'arr_complain' => $arr_complain,
        ];
        return resSuccessWithinData($data_result);
    }

    public function getFastType7($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $complainModel = \App::make('App\Models\Complain');
        $orderPackageModel = \App::make('App\Models\OrderPackage');

        $orderM1  = $orderModel;
        $complainM  = $complainModel;
        $orderPackageDB = $orderPackageModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $complainM = $complainM->where('customer_id', $customer->id);
                $orderPackageDB = $orderPackageDB->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_order = [
            'total_not_done' => 0,  // Chưa hoàn thành (tất cả các đơn trừ tt đã hoàn thành + đã hủy ạ)
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Chờ đặt cọc
            'total_ordered' => 0, // Đã đặt hàng
        ];

        $select = 'sum(if(orders.status not in ( \''. OrderConstant::KEY_STATUS_DONE .'\', \''. OrderConstant::KEY_STATUS_CANCEL .'\'), 1, 0)) as total_not_done,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_order['total_not_done']   = $result_1->total_not_done == null ? 0 : (int)$result_1->total_not_done;
            $arr_order['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_order['total_waiting_deposit']   = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_order['total_ordered']   = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
        }


        // Kiện
        $arr_pack = [
            'total_pack_not_take' => 0, // Tổng kiện hàng chưa lấy (kiện ở tt Đến kho vn a)
        ];

        $orderPackageDB = $orderPackageDB->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                                            ->where('status', PackageConstant::STATUS_WAREHOUSE_VN);

        $select = 'count(*) as total_pack_not_take';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->first();

        if ($orderPackageV1) {
            $arr_pack['total_pack_not_take']   = $orderPackageV1->total_pack_not_take == null ? 0 : (int)$orderPackageV1->total_pack_not_take;
        }

        // Khiếu nại
        $arr_complain = [
            'total_pending' => 0, // Tổng khiếu nại chưa xử lý
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_complain,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PENDING .'\', 1, 0)) as total_pending,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESSED .'\', 1, 0)) as total_done';
        $result_3 = $complainM->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();
        if ($result_3) {
            $arr_complain['total_pending'] = $result_3->total_pending == null ? 0 : (int)$result_3->total_pending;
            $arr_complain['total_done']    = $result_3->total_done == null ? 0 : (int)$result_3->total_done;
        }

        // Khách hàng
        $arr_customer = [
            'total_care' => 0, // Khách hàng chăm sóc
            'total_transfer_from_tu_van' => 0, // Khách từ tư vấn chuyển sang
            'total_order' => 0, // Khách hàng order
            'total_consignment' => 0, // Khách hàng kí gửi
        ];
        $customerModel = \App::make('App\Models\Customer');

        $select = 'count(*) as total_new,
                   sum(if(customers.service = 0, 1, 0)) as total_order,
                   sum(if(customers.service = 1, 1, 0)) as total_consignment';

        $result_4 = $customerModel->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_4) {
            $arr_customer['total_order']    = $result_4->total_order == null ? 0 : (int)$result_4->total_order;
            $arr_customer['total_consignment']    = $result_4->total_consignment == null ? 0 : (int)$result_4->total_consignment;
        }


        $data_result = [
            'arr_order'    => $arr_order,
            'arr_pack'     => $arr_pack,
            'arr_complain' => $arr_complain,
            'arr_customer' => $arr_customer,
        ];
        return resSuccessWithinData($data_result);
    }

    public function getFastType8($time, $customer_search)
    {
        $orderModel = \App::make('App\Models\Order');
        $complainModel = \App::make('App\Models\Complain');
        $orderPackageModel = \App::make('App\Models\OrderPackage');

        $orderM1  = $orderModel;
        $complainM  = $complainModel;
        $orderPackageDB = $orderPackageModel;

        if (!is_null($customer_search)) {
            $customerModel = \App::make('App\Models\Customer');
            $customer = $customerModel->where('code', $customer_search)
                                    ->orWhere('phone_number', $customer_search)
                                    ->orWhere('email', $customer_search)
                                    ->first();
            if ($customer) {
                $orderM1 = $orderM1->where('customer_id', $customer->id);
                $complainM = $complainM->where('customer_id', $customer->id);
                $orderPackageDB = $orderPackageDB->where('customer_id', $customer->id);
            } else {
                $result = ['error' => 'Không tìm thấy khách hàng hợp lệ'];
                return resErrorWithinData($result, 'Có lỗi xảy ra', 422);
            }
        }

        $dateRange = $this->perDateRange($time);

        // Đơn hàng
        $arr_order = [
            'total_waiting_quote' => 0, // Chờ báo giá
            'total_waiting_deposit' => 0, // Chờ đặt cọc
            'total_ordered' => 0, // Đã đặt hàng
            'total_done' => 0, // Hoàn thành
            'total_order_fee' => 0, // Tổng doanh số dịch vụ (phí đặt hàng của đơn hoàn thành)
        ];

        $select = 'sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', order_fee, 0)) as total_order_fee,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_waiting_quote,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_DEPOSIT .'\', 1, 0)) as total_waiting_deposit,
                   sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered';

        $result_1 = $orderM1->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();

        if ($result_1) {
            $arr_order['total_done']   = $result_1->total_done == null ? 0 : (int)$result_1->total_done;
            $arr_order['total_waiting_quote']   = $result_1->total_waiting_quote == null ? 0 : (int)$result_1->total_waiting_quote;
            $arr_order['total_waiting_deposit']   = $result_1->total_waiting_deposit == null ? 0 : (int)$result_1->total_waiting_deposit;
            $arr_order['total_ordered']   = $result_1->total_ordered == null ? 0 : (int)$result_1->total_ordered;
            $arr_order['total_order_fee']   = $result_1->total_order_fee == null ? 0 : (int)$result_1->total_order_fee;
        }

        // Kiện
        $arr_pack = [
            'total_pack_not_take' => 0, // Tổng kiện hàng chưa lấy (kiện ở tt Đến kho vn a)
        ];

        $orderPackageDB = $orderPackageDB->whereBetween('created_at', [$dateRange['startDate'], $dateRange['endDate']])
                                            ->where('status', PackageConstant::STATUS_WAREHOUSE_VN);

        $select = 'count(*) as total_pack_not_take';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->first();

        if ($orderPackageV1) {
            $arr_pack['total_pack_not_take']   = $orderPackageV1->total_pack_not_take == null ? 0 : (int)$orderPackageV1->total_pack_not_take;
        }

        // Khiếu nại
        $arr_complain = [
            'total_pending' => 0, // Tổng khiếu nại chờ xử lý
            'total_process' => 0, // Tổng khiếu nại đang xử lý
            'total_done' => 0, // Hoàn thành
        ];

        $select = 'count(*) as total_all_complain,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PENDING .'\', 1, 0)) as total_pending,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESS .'\', 1, 0)) as total_process,
                   sum(if(complains.status = \''. ComplainConstant::KEY_STATUS_PROCESSED .'\', 1, 0)) as total_done';
        $result_3 = $complainM->whereBetween('updated_at', [$dateRange['startDate'], $dateRange['endDate']])
                            ->selectRaw($select)->first();
        if ($result_3) {
            $arr_complain['total_pending'] = $result_3->total_pending == null ? 0 : (int)$result_3->total_pending;
            $arr_complain['total_process'] = $result_3->total_process == null ? 0 : (int)$result_3->total_process;
            $arr_complain['total_done']    = $result_3->total_done == null ? 0 : (int)$result_3->total_done;
        }


        $data_result = [
            'arr_order'    => $arr_order,
            'arr_pack'     => $arr_pack,
            'arr_complain' => $arr_complain,
        ];
        return resSuccessWithinData($data_result);
    }

    public static function perDateRange($time)
    {
        $endDate = Carbon::now();

        if ($time == 'd') {
            $startDate =  Carbon::today()->startOfDay();
        }
        if ($time == 'w'){
            $startDate = Carbon::now()->startOfWeek();
        }
        if ($time == 'm'){
            $startDate = Carbon::now()->firstOfMonth();
        }
        if ($time == 'q'){
            $startDate = Carbon::now()->startOfQuarter();
        }
        if ($time == 'y'){
            $startDate = Carbon::now()->startOfYear();
        }

        if ($time == 'd'){
            $startPrevious = Carbon::today()->subDay()->startOfDay();
            $endPrevious = Carbon::today()->subDay()->endOfDay();
        }
        if ($time == 'w'){
            $startPrevious = Carbon::today()->subWeek()->startOfWeek();
            $endPrevious = Carbon::today()->subWeek()->endOfWeek();
        }
        if ($time == 'm'){
            $startPrevious = Carbon::today()->subMonth()->startOfMonth();
            $endPrevious = Carbon::today()->subMonth()->endOfMonth();
        }
        if ($time == 'q'){
            $startPrevious = Carbon::today()->subMonth(3)->startOfQuarter();
            $endPrevious = Carbon::today()->subMonth(3)->endOfQuarter();
        }
        if ($time == 'y'){
            $startPrevious = Carbon::today()->subMonth(12)->startOfYear();
            $endPrevious = Carbon::today()->subMonth(12)->endOfYear();
        }

         $result = array(
            'startDate'     => $startDate->format('Y-m-d H:i:s'),
            'endDate'       => $endDate->format('Y-m-d H:i:s'),
            'startPrevious' => $startPrevious->format('Y-m-d H:i:s'),
            'endPrevious'   => $endPrevious->format('Y-m-d H:i:s'),
        );
        return $result;
    }


    public function slow(Request $request)
    {
        $user_type = $request->get('user_type', 0);
        if ($user_type == 0) {
            return resSuccess('Done');
        }
        if ($user_type == 2) {
            return $this->getSlowType2();
        }
        if ($user_type == 3) {
            return $this->getSlowType3();
        }
        if ($user_type == 4) {
            return $this->getSlowType4();
        }
        if ($user_type == 5) {
            return $this->getSlowType5();
        }
        return resSuccess('OK');
    }

    public function getSlowType2()
    {
        $orderModel = \App::make('App\Models\Order');
        $dateRangeM = $this->perDateRange('m');
        $dateRangeD = $this->perDateRange('d');
        $dateRangeY = $this->perDateRange('y');

        // Nhân viên báo giá
        // Nhân viên báo giá
        // Nhân viên báo giá

        // Theo tháng
        $orderM1 = $orderModel;
        $orderM1 = $orderM1->leftJoin('users', 'orders.staff_quotation_id', '=', 'users.id');

        $orderM1 = $orderM1->whereBetween('orders.created_at', [$dateRangeM['startDate'], $dateRangeM['endDate']])
                            ->whereNotNull('staff_quotation_id');

        $select = 'orders.staff_quotation_id, users.name as staff_name,
                   count(*) as total_order';
        $orderM1 = $orderM1->selectRaw($select)->groupBy('staff_quotation_id')
                                    ->orderBy('total_order', 'DESC')
                                    ->offset(0)
                                    ->limit(7)
                                    ->get();

        $quotation_chart_month = [];
        if ($orderM1) {
            foreach ($orderM1 as $key => $item) {
                $temp = [
                    $item->staff_name,
                    $item->total_order
                ];
                array_push($quotation_chart_month, $temp);
            }
        }

        // Theo ngày
        $orderD1 = $orderModel;
        $orderD1 = $orderD1->leftJoin('users', 'orders.staff_quotation_id', '=', 'users.id');

        $orderD1 = $orderD1->whereBetween('orders.created_at', [$dateRangeD['startDate'], $dateRangeD['endDate']])
                            ->whereNotNull('staff_quotation_id');

        $select = 'orders.staff_quotation_id, users.name as staff_name,
                   count(*) as total_order';
        $orderD1 = $orderD1->selectRaw($select)->groupBy('staff_quotation_id')
                                    ->orderBy('total_order', 'DESC')
                                    ->offset(0)
                                    ->limit(7)
                                    ->get();

        $quotation_chart_day = [];
        if ($orderD1) {
            foreach ($orderD1 as $key => $item) {
                $temp = [
                    $item->staff_name,
                    $item->total_order
                ];
                array_push($quotation_chart_day, $temp);
            }
        }


        // Nhân viên đặt hàng
        // Nhân viên đặt hàng
        // Nhân viên đặt hàng

        $orderM1 = $orderModel;
        $orderM1 = $orderM1->leftJoin('users', 'orders.staff_order_id', '=', 'users.id');

        $orderM1 = $orderM1->whereBetween('orders.created_at', [$dateRangeM['startDate'], $dateRangeM['endDate']])
                            ->whereNotNull('staff_order_id');

        $select = 'orders.staff_order_id, users.name as staff_name,
                   count(*) as total_order';
        $orderM1 = $orderM1->selectRaw($select)->groupBy('staff_order_id')
                                    ->orderBy('total_order', 'DESC')
                                    ->offset(0)
                                    ->limit(7)
                                    ->get();

        $order_chart_month = [];
        if ($orderM1) {
            foreach ($orderM1 as $key => $item) {
                $temp = [
                    $item->staff_name,
                    $item->total_order
                ];
                array_push($order_chart_month, $temp);
            }
        }

        // Theo ngày
        $orderD1 = $orderModel;
        $orderD1 = $orderD1->leftJoin('users', 'orders.staff_order_id', '=', 'users.id');

        $orderD1 = $orderD1->whereBetween('orders.created_at', [$dateRangeD['startDate'], $dateRangeD['endDate']])
                            ->whereNotNull('staff_order_id');

        $select = 'orders.staff_order_id, users.name as staff_name,
                   count(*) as total_order';
        $orderD1 = $orderD1->selectRaw($select)->groupBy('staff_order_id')
                                    ->orderBy('total_order', 'DESC')
                                    ->offset(0)
                                    ->limit(7)
                                    ->get();

        $order_chart_day = [];
        if ($orderD1) {
            foreach ($orderD1 as $key => $item) {
                $temp = [
                    $item->staff_name,
                    $item->total_order
                ];
                array_push($order_chart_day, $temp);
            }
        }

        // Nhân viên khiếu nại
        // Nhân viên khiếu nại
        // Nhân viên khiếu nại

        $complainModel = \App::make('App\Models\Complain');
        $complainM = $complainModel;
        // $complainM = $complainModel->leftJoin('users', 'complains.staff_complain_id', '=', 'users.id');

        $complainM = $complainM->whereBetween('complains.created_at', [$dateRangeY['startDate'], $dateRangeY['endDate']]);

        $dataFormat = 'DATE_FORMAT(complains.created_at, "%m-%Y")';
        $dataFormatShow = 'DATE_FORMAT(complains.created_at, "%m")';

        $select = $dataFormat . 'as create_time,' . $dataFormatShow . 'as create_time_show,
                count(*) as total_complain,
                complains.staff_complain_id';

        $complainM = $complainM->selectRaw($select)->groupBy('create_time')
                                    ->orderBy('create_time_show', 'DESC')
                                    ->withoutGlobalScope(\App\Scopes\SortByCreatedDescScope::class)
                                    ->get();

        $complain_chart_month = [];
        for ($i=1; $i < 13; $i++) {
            array_push($complain_chart_month, 0);
        }

        if ($complainM) {
            foreach ($complainM as $key => $complain) {
                $timeM = (int)$complain->create_time_show;
                $complain_chart_month[$timeM - 1] = $complain['total_complain'];
            }
        }

        $data_result = [
            'chart_user_type_2'  => [
                'staff_quotation' => [
                    'chart_month' => $quotation_chart_month,
                    'chart_day' => $quotation_chart_day
                ],
                'staff_order' => [
                    'chart_month' => $order_chart_month,
                    'chart_day' => $order_chart_day
                ],
                'staff_complain' => [
                    'chart_month' => $complain_chart_month,
                ]
            ],
        ];
        return resSuccessWithinData($data_result);
    }

    public function getSlowType3()
    {
        $data_1 = $this->getQuote('m');
        $data_2 = $this->getQuote('y');

        $data_result = [
            'chart_user_type_3'  => [
                'chart_day' => $data_1,
                'chart_month' => $data_2
            ],
        ];
        return resSuccessWithinData($data_result);
    }

    public function getQuote($time)
    {
        $orderModel = \App::make('App\Models\Order');
        $orderPackageModel = \App::make('App\Models\OrderPackage');
        $dateRangeY = $this->perDateRange('y');
        if ($time == 'm') {
            $dateRangeY = $this->perDateRange('m');
        }

        $chart_order_fee = [];
        $chart_inspection_cost = [];
        $chart_woodworking_cost = [];
        $chart_order_shiping_cost = [];

        $endFor = 13;
        if ($time == 'm') {
            $endFor = 32;
        }

        for ($i = 1; $i < $endFor; $i++) {
            array_push($chart_order_fee, 0);
            array_push($chart_inspection_cost, 0);
            array_push($chart_woodworking_cost, 0);
            array_push($chart_order_shiping_cost, 0);
        }

        $orderDB = $orderModel;
        $orderV1 = $orderDB->whereBetween('created_at', [$dateRangeY['startDate'], $dateRangeY['endDate']]);

        $dataFormat = 'DATE_FORMAT(created_at, "%m-%Y")';
        $dataFormatShow = 'DATE_FORMAT(created_at, "%m")';

        if ($time == 'm') {
            $dataFormat = 'DATE_FORMAT(created_at, "%d-%m-%Y")';
            $dataFormatShow = 'DATE_FORMAT(created_at, "%d")';
        }

        $select = $dataFormat . 'as create_time,' . $dataFormatShow . 'as create_time_show,
                sum(order_fee) as total_order_fee,
                sum(inspection_cost) as total_inspection_cost,
                id';

        $orderV1 = $orderV1->selectRaw($select)->groupBy('create_time')
                                    ->orderBy('create_time_show', 'DESC')
                                    ->get();

        if ($orderV1) {
            foreach ($orderV1 as $key => $order) {
                $timeM = (int)$order->create_time_show;
                $chart_order_fee[$timeM - 1] = (int)$order['total_order_fee'];
                $chart_inspection_cost[$timeM - 1] = (int)$order['total_inspection_cost'];
            }
        }

        // Đếm phí đóng gỗ
        $orderPackageDB = $orderPackageModel;
        $orderPackageDB = $orderPackageModel->whereBetween('created_at', [$dateRangeY['startDate'], $dateRangeY['endDate']]);

        $select = $dataFormat . 'as create_time,' . $dataFormatShow . 'as create_time_show,
                sum(woodworking_cost) as total_woodworking_cost,
                id';

        $orderPackageV1 = $orderPackageDB->selectRaw($select)->groupBy('create_time')
                                    ->orderBy('create_time_show', 'DESC')
                                    ->get();
        if ($orderPackageV1) {
            foreach ($orderPackageV1 as $key => $order) {
                $timeM = (int)$order->create_time_show;
                $chart_woodworking_cost[$timeM - 1] = (int)$order['total_woodworking_cost'];
            }
        }

        // Phí đàm phán
        $arr_status = [
            OrderConstant::KEY_STATUS_WAIT_TO_PAY, // Chờ thanh toán
            OrderConstant::KEY_STATUS_ORDERED, // Đặt hàng
            OrderConstant::KEY_STATUS_DONE, // Đã hoàn thành
        ];
        $orderV2 = $orderModel;
        $orderV2 = $orderV2->whereIn('status', $arr_status)->leftJoin('order_status_times', 'orders.id', '=', 'order_status_times.order_id');

        // $av = $orderV2->get();

        // foreach ($av as $key => $e) {
        //     dd($e->china_shipping_cost_old);
        //     $a = $e->order_cost_old - $e->order_cost + $e->china_shipping_cost_old - $e->china_shipping_cost;
        //     dd($a);
        // }
        // dd($orderV2->get());

        // Nhóm theo thời điểm Chờ thanh toán
        $dataFormat = 'DATE_FORMAT(order_status_times.status_8, "%m-%Y")';
        $dataFormatShow = 'DATE_FORMAT(order_status_times.status_8, "%m")';

        if ($time == 'm') {
            $dataFormat = 'DATE_FORMAT(order_status_times.status_8, "%d-%m-%Y")';
            $dataFormatShow = 'DATE_FORMAT(order_status_times.status_8, "%d")';
        }

        $select = $dataFormat . 'as create_time,' . $dataFormatShow . 'as create_time_show,
                sum(orders.order_cost_old - orders.order_cost + orders.china_shipping_cost_old - orders.china_shipping_cost) as total_order_shiping_cost,
                orders.id';

        $orderV2 = $orderV2->selectRaw($select)->groupBy('create_time')
                                    ->orderBy('create_time_show', 'DESC')
                                    ->get();
        if ($orderV2) {
            foreach ($orderV2 as $key => $order) {
                $timeM = (int)$order->create_time_show;
                $chart_order_shiping_cost[$timeM - 1] = (int)$order['total_order_shiping_cost'];
            }
        }

        return [
            [
                "name" => "Phí đặt hàng",
                "data" => $chart_order_fee
            ],
            [
                "name" => "Phí đàm phán",
                "data" => $chart_order_shiping_cost
            ],
            [
                "name" => "Phí kiểm đếm",
                "data" => $chart_inspection_cost
            ],
            [
                "name" => "Phí đóng gỗ",
                "data" => $chart_woodworking_cost
            ],
        ];
    }

    public function getSlowType4()
    {
        $data_1 = $this->getOrder('m');
        $data_2 = $this->getOrder('y');

        $data_result = [
            'chart_user_type_4'  => [
                'chart_day' => $data_1,
                'chart_month' => $data_2
            ],
        ];
        return resSuccessWithinData($data_result);
    }

    public function getOrder($time)
    {
        $orderModel = \App::make('App\Models\Order');
        $orderPackageModel = \App::make('App\Models\OrderPackage');
        $dateRangeY = $this->perDateRange('y');
        if ($time == 'm') {
            $dateRangeY = $this->perDateRange('m');
        }

        $chart_new = []; // Đơn hàng mới
        $chart_ordered = []; // Đã đặt hàng
        $chart_done = []; // Đã hoàn thành
        $chart_not_done = []; // Chưa hoàn thành, tất cả các đơn trừ tt đã hoàn thành + đã hủy

        $endFor = 13;
        if ($time == 'm') {
            $endFor = 32;
        }

        for ($i = 1; $i < $endFor; $i++) {
            array_push($chart_new, 0);
            array_push($chart_ordered, 0);
            array_push($chart_done, 0);
            array_push($chart_not_done, 0);
        }

        $orderDB = $orderModel;
        $orderV1 = $orderDB->whereBetween('created_at', [$dateRangeY['startDate'], $dateRangeY['endDate']]);

        $dataFormat = 'DATE_FORMAT(created_at, "%m-%Y")';
        $dataFormatShow = 'DATE_FORMAT(created_at, "%m")';

        if ($time == 'm') {
            $dataFormat = 'DATE_FORMAT(created_at, "%d-%m-%Y")';
            $dataFormatShow = 'DATE_FORMAT(created_at, "%d")';
        }

        $select = $dataFormat . 'as create_time,' . $dataFormatShow . 'as create_time_show,
                sum(if(orders.status = \''. OrderConstant::KEY_STATUS_WAITING_QUOTE .'\', 1, 0)) as total_new,
                sum(if(orders.status = \''. OrderConstant::KEY_STATUS_ORDERED .'\', 1, 0)) as total_ordered,
                sum(if(orders.status = \''. OrderConstant::KEY_STATUS_DONE .'\', 1, 0)) as total_done,
                sum(if(orders.status not in ( \''. OrderConstant::KEY_STATUS_DONE .'\', \''. OrderConstant::KEY_STATUS_CANCEL .'\'), 1, 0)) as total_not_done,
                id';

        $orderV1 = $orderV1->selectRaw($select)->groupBy('create_time')
                                    ->orderBy('create_time_show', 'DESC')
                                    ->get();
        if ($orderV1) {
            foreach ($orderV1 as $key => $order) {
                $timeM = (int)$order->create_time_show;
                $chart_new[$timeM - 1] = (int)$order['total_new'];
                $chart_ordered[$timeM - 1] = (int)$order['total_ordered'];
                $chart_done[$timeM - 1] = (int)$order['total_done'];
                $chart_not_done[$timeM - 1] = (int)$order['total_not_done'];
            }
        }

        return [
            [
                "name" => "Đơn hàng mới",
                "data" => $chart_new
            ],
            [
                "name" => "Đã đặt hàng",
                "data" => $chart_ordered
            ],
            [
                "name" => "Đã hoàn thành",
                "data" => $chart_done
            ],
            [
                "name" => "Chưa hoàn thành",
                "data" => $chart_not_done
            ],
        ];
    }

    public function getSlowType5()
    {
        $data_1 = $this->getOrderGroup('m');
        $data_2 = $this->getOrderGroup('y');

        $data_result = [
            'chart_user_type_4'  => [
                'chart_day' => $data_1,
                'chart_month' => $data_2
            ],
        ];
        return resSuccessWithinData($data_result);
    }

    public function getOrderGroup($time)
    {
        $orderModel = \App::make('App\Models\Order');
        $dateRange = $this->perDateRange('y');
        if ($time == 'm') {
            $dateRange = $this->perDateRange('m');
        }
        $orderDB = $orderModel->leftJoin('customers', 'orders.customer_id', '=', 'customers.id');
        $orderDB = $orderDB->join('order_package', 'orders.id', '=', 'order_package.order_id');
        $orderDB = $orderDB->where('orders.status', OrderConstant::KEY_STATUS_DONE)
                            ->whereNotNull('customers.staff_counselor_id')
                            ->whereBetween('orders.created_at', [$dateRange['startDate'], $dateRange['endDate']]);

        $select = 'sum(orders.order_cost + orders.order_fee + orders.inspection_cost + orders.china_shipping_cost * orders.exchange_rate) + sum(order_package.insurance_cost + order_package.woodworking_cost + order_package.international_shipping_cost + order_package.shock_proof_cost + order_package.storage_cost + order_package.delivery_cost) as total_full,
            customers.staff_counselor_id';


        $orderDB = $orderDB->selectRaw($select)->groupBy('customers.staff_counselor_id')
                                    ->orderBy('total_full', 'DESC')
                                    ->offset(0)
                                    ->limit(7)
                                    ->get();
        if ($orderDB) {
            $userModel = \App::make('App\Models\User');
            $arr_result = [];
            foreach ($orderDB as $key => $item) {
                $user = $userModel->where('id', $item->staff_counselor_id)->first();
                $temp = [
                    'user_name' => $user->name,
                    'total_full' => $item->total_full,
                ];
                array_push($arr_result, $temp);
            }
            dd($arr_result);
        }
    }
}
