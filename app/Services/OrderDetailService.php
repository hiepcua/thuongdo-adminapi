<?php


namespace App\Services;


use App\Constants\TimeConstant;
use App\Models\OrderDetail;

class OrderDetailService extends BaseService
{
    public function insertFromOrder(string $orderId, array $orderDetail)
    {
        foreach ($orderDetail as $key => $item) {
            $this->removeKey($item);
            $item['id'] = getUuid();
            $item['created_at'] = date(TimeConstant::DATETIME);
            $item['order_id'] = $orderId;
            $item['amount_cny'] = $item['unit_price_cny'] * $item['quantity'];
            $orderDetail[$key] = $item;
        }
        OrderDetail::query()->insert($orderDetail);
    }

    private function removeKey(array &$item)
    {
        $diffs = array_diff(array_keys($item), (new OrderDetail())->getFillable());
        if (!$diffs) {
            return;
        }
        $diffs = array_values($diffs);
        foreach ($diffs as $diff) {
            unset($item[$diff]);
        }
    }
}