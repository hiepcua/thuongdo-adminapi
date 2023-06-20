<?php


namespace App\Services;

use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Constants\ReportConstant;
use App\Constants\TimeConstant;
use App\Helpers\AccountingHelper;
use App\Models\Category;
use App\Models\Consignment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\Province;
use App\Models\ReportCustomer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportCeoService implements Service
{
    public function getReportCEO(): array
    {
        $data['general'] = $this->getReportGeneralCEO();
        $data['charts'] = [
            'services' => $this->getChartServices(
                [
                    ['name' => 'Phí đặt hàng', 'model' => Order::class, 'column' => 'order_fee'],
                    [
                        'name' => 'Phí đàm phán',
                        'model' => Order::class,
                        'column' => 'order_cost,order_cost_old,china_shipping_cost,china_shipping_cost_old'
                    ],
                    ['name' => 'Phí kiểm đếm', 'model' => Order::class, 'column' => 'inspection_cost'],
                    ['name' => 'Phí đóng gỗ', 'model' => OrderPackage::class, 'column' => 'woodworking_cost'],
                ]
            ),
            'warehouses' => $this->getChartWarehouses(),
            'categories' => $this->getChartCategories()
        ];
        return $data;
    }

    /**
     * Thông kê các dịch vụ
     * @param $array
     * @return array
     */
    private function getChartServices($array): array
    {
        $data = [];
        foreach ($array as $key => $item) {
            $data[$key]['name'] = $item['name'];
            for ($i = 1; $i < 13; $i++) {
                $time = Carbon::parse(date('Y')."-$i-01");
                $from = $time->startOfMonth()->format(TimeConstant::DATE).' 00:00:00';
                $to = $time->endOfMonth()->format(TimeConstant::DATE).' 23:59:59';
                $result = (new $item['model'])->whereBetween('created_at', [$from, $to])->get();
                if (($i > (int)date('m')) || !$result) {
                    $data[$key]['data'][] = null;
                    continue;
                }

                $columns = explode(',', $item['column']);
                $total = $result->sum($item['column']);
                
                // Phí đàm phán
                if (count($columns) > 1) {
                    
                    $result = $result->whereIn('status', [OrderConstant::KEY_STATUS_ORDERED, OrderConstant::KEY_STATUS_DEPOSITED, OrderConstant::KEY_STATUS_DONE]);
                    
                    $total =
                         $result->sum('order_cost_old') - $result->sum('order_cost') + $result->sum(
                            'china_shipping_cost_old'
                        ) - $result->sum(
                            'china_shipping_cost'
                        );
                }
                $data[$key]['data'][] = AccountingHelper::getCosts($total);
            }
        }

        return $data;
    }

    public function getChartWarehouses(?string $warehouseId = null): array
    {
        $provinces = Province::query()->whereIn(
            'name',
            ['Hà Nội', 'Hải Phòng', 'Hồ Chí Minh']
        )->pluck('name', 'id')->all();

        $data = [];

        foreach ($provinces as $id => $province) {
            $data[$id] = ['name' => $province];
            for ($i = 1; $i < 13; $i++) {
                $time = Carbon::parse(date('Y')."-$i-01");
                $from = $time->startOfMonth()->format(TimeConstant::DATE).' 00:00:00';
                $to = $time->endOfMonth()->format(TimeConstant::DATE).' 23:59:59';
                $orders = Order::query()->where('warehouse_id', $warehouseId ? '=' : '!=', $warehouseId)->whereHas(
                    'warehouse',
                    function ($q) use ($id) {
                        $q->where('province_id', $id);
                    }
                )->whereBetween('created_at', [$from, $to])->get();
                $consignments = Consignment::query()->where(
                    'warehouse_vi',
                    $warehouseId ? '=' : '!=',
                    $warehouseId
                )->whereHas(
                    'warehouse.province',
                    function ($q) use ($id) {
                        return $q->where('id', $id);
                    }
                )->whereBetween('created_at', [$from, $to])->get();
                if (($i > (int)date('m')) || (!$orders && !$consignments)) {
                    $data[$id]['data'][] = null;
                    continue;
                }
                $data[$id]['data'][] = $orders->count() + $consignments->count();
            }
        }
        return array_values($data);
    }

    public function getChartCategories(?string $type = 'day'): array
    {
        $range = $this->getFromTo($type);
        $report = OrderDetail::query()->whereBetween('order_details.created_at', $range)->join(
            'categories',
            'categories.id',
            '=',
            'order_details.category_id'
        )->whereBetween('order_details.created_at', $range)->selectRaw(
            'count(*) as count, order_details.category_id, categories.name'
        )->groupBy('order_details.category_id')->orderByDesc('count')->get();

        return request()->query('type') && request()->query('type') == 'percent' ? $this->getChartCategoriesByPercent(
            $report, $type
        ) : $this->getChartCategoriesByCount($report);
    }

    private function getFromTo(?string $type = 'day', ?int $subtract = 0): array
    {
        $from = Carbon::now()->subDays($subtract)->startOfDay()->format(TimeConstant::DATETIME);
        $to = Carbon::now()->subDays($subtract)->endOfDay()->format(TimeConstant::DATETIME);
        if ($type != 'day') {
            $from = Carbon::now()->subMonths($subtract)->startOfMonth()->format(TimeConstant::DATETIME);
            $to = Carbon::now()->subMonths($subtract)->endOfMonth()->format(TimeConstant::DATETIME);
        }
        return [$from, $to];
    }

    private function getChartCategoriesByPercent(Collection $report, string $type): array
    {
        $data = [];
        $total = $report->sum('count');
        foreach ($report as $item) {
            $data[$item->category_id] = [
                'name' => $item->name,
                'quantity' => $item->count,
                'percent' => roundXPrecision($item->count / $total * 100),
                'quantity_old' => 0
            ];
            if (count($data) == 7) {
                break;
            }
        }
        if (count($data) <= 7) {
            foreach (
                Category::query()->whereNotIn('id', $report->pluck('category_id')->all())->take(7 - count($data))->get(
                ) as $item
            ) {
                $data[$item->id] = [
                    'name' => $item->name,
                    'quantity' => 0,
                    'percent' => 0,
                    'quantity_old' => 0,
                ];
            }
        }
        foreach (
            OrderDetail::query()->whereBetween('created_at', $this->getFromTo($type, 1))->whereIn(
                'category_id',
                array_keys($data)
            )->selectRaw(
                'count(*) as count, category_id'
            )->groupBy('category_id')->get() as $item
        ) {
            $data[$item->category_id]['quantity_old'] = $item->count;
        }
        return array_values($data);
    }

    private function getChartCategoriesByCount(Collection $report): array
    {
        $data = [];
        foreach ($report as $item) {
            $data[] = [$item->name, $item->count];
            if (count($data) == 7) {
                break;
            }
        }
        if (count($data) <= 7) {
            foreach (
                Category::query()->whereNotIn('id', $report->pluck('category_id')->all())->take(7 - count($data))->get(
                ) as $item
            ) {
                $data[] = [$item->name, 0];
            }
        }
        return $data;
    }

    private function getReportGeneralCEO(): array
    {
        $data['revenue'] = $this->getRevenue();
        $data['shipping'] = $this->getShipping();
        $data['balance'] = (float)ReportCustomer::query()->sum('balance_amount');
        $data['unpaid'] = AccountingHelper::getCosts(
            Order::query()->whereBetween('deposit_percent', [1, 99])->get()->sum('debt_cost')
        );
        $data['money_receivable'] = AccountingHelper::getCosts(
            OrderPackage::query()->where('status', PackageConstant::STATUS_WAREHOUSE_VN)->get()->sum('amount')
        );
        $data['fund_china'] = [
            'cny' => $amount = (new FundService())->getBalanceFundChina(),
            'vnd' => AccountingHelper::getCosts($amount * getExchangeRate())
        ];
        return $data;
    }

    private function getRevenue(): array
    {
        $current = date('Y');
        $last = $current - 1;
        $data[$current]['value'] = 0;
        $data[$last]['value'] = 0;
        foreach (
            Order::query()->whereYear('created_at', $current)->orWhereYear('created_at', $last)->cursor() as $order
        ) {
            $year = explode('-', $order->created_at);
            $data[$year[0]]['value'] += $order->total_amount;
        }
        return array_values($data);
    }

    private function getShipping(): array
    {
        $volume = 0;
        $weight = 0;
        foreach (OrderPackage::query()->cursor() as $package) {
            $volume += $package->volume;
            $weight += $package->weight;
        }
        return [
            ['key' => ReportConstant::SHIPPING_KG, 'value' => $weight],
            ['key' => ReportConstant::SHIPPING_M3, 'value' => $volume]
        ];
    }

}
