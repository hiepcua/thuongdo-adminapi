<?php

namespace App\Observers;

use App\Helpers\AccountingHelper;
use App\Models\OrderDetail;
use App\Models\OrderSupplier;
use App\Services\AccountingService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

class OrderDetailObserve
{
    public function deleted(OrderDetail $detail)
    {
        $orderSupplier = OrderSupplier::query()->where(
            ['order_id' => $detail->order_id, 'supplier_id' => $detail->supplier_id]
        );
        $this->decrementByKey($orderSupplier, 'order_cost', $detail->order_cost ?? 0);
        (new OrderService())->orderCancelIfNotExistSupplier($detail->order_id);
    }

    public function updated(OrderDetail $detail)
    {
        if($detail->getOriginal('amount_cny') != $detail->amount_cny)
        {
            $condition = ['order_id' => $detail->order_id, 'supplier_id' => $detail->supplier_id];
            $totalCNY = OrderDetail::query()->where($condition)->sum('amount_cny');
            $data['order_cost'] = $orderCost = AccountingHelper::getCosts($totalCNY * getExchangeRate($detail->order_id));
            $data['order_fee'] = (new AccountingService())->getOrderFee($orderCost);
            OrderSupplier::query()->where($condition)->update($data);
        }
    }

    /**
     * @param $query
     * @param  string  $key
     * @param  float  $amount
     */
    private function decrementByKey($query, string $key, float $amount): void
    {
        $report = $query->first();
        if(!$report) return;
        if ($report->{$key} <= $amount) {
            $report->delete();
            return;
        }
        $report->decrement($key, $amount);
        $report->order_fee = (new AccountingService())->getOrderFee($report->order_cost);
        $report->save();
    }
}
