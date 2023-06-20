<?php


namespace App\Services;

use App\Constants\OrderConstant;
use App\Constants\PackageConstant;
use App\Helpers\AccountingHelper;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderDetailPackage;
use App\Models\OrderNote;
use App\Models\OrderPackage;
use App\Models\OrderStatusTime;
use App\Models\OrderSupplier;
use App\Models\ReportCustomer;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService extends BaseService
{
    protected string $_resource = OrderResource::class;

    /**
     * Tính khoản tạm ứng, tiền chi phi vận chuyển
     * @param  string  $orderId
     * @param  string  $key
     * @param  float  $value
     * @param  string|null  $customerId
     */
    public function reportOrderToVN(string $orderId, string $key, float $value, ?string $customerId = null): void
    {
        $customer = $customerId ?? Auth::user()->id;
        $report = ReportCustomer::query()->where(['customer_id' => $customer, 'order_id', $orderId])->first();
        $report->{$key} += $value;
        $report->save();
    }

    /**
     * @param  string  $orderId
     * @param  string  $status
     */
    public function setStatusTime(string $orderId, string $status)
    {
        $order = OrderStatusTime::query()->firstOrCreate(['order_id' => $orderId]);
        $order->{$status} = now();
        $order->save();
    }

    /**
     * @param  string  $id
     */
    public function deleteByOrderId(string $id): void
    {
        foreach (OrderPackage::query()->where('order_id', $id)->cursor() as $package) {
            $package->delete();
        }
        OrderDetail::query()->where('order_id', $id)->delete();
        OrderSupplier::query()->where('order_id', $id)->delete();
    }

    /**
     * @param  string  $id
     * @param  array  $data
     * @return Builder|Builder[]|Collection|Model|null
     * @throws \Exception
     */
    public function update(string $id, array $data)
    {
        return DB::transaction(function() use($id, $data) {
            $this->updateOrderRemoves($id, $data['removes'] ?? []);
            $this->updateOrderModifies($id, $data);
            $this->updateOrderCost($id);

            $order = Order::query()->find($id);
            if($order->status == OrderConstant::KEY_STATUS_WAITING_QUOTE) {
                $data['status'] = OrderConstant::KEY_STATUS_WAITING_DEPOSIT;
            }

            $this->updateOrderSupplier($order);

            // Cập nhật lịch sử tiền phí ship, tiền hàng khi ở trạng thái chờ báo giá -> đặt cọc
            $this->updateSupplierOrderDetails($order);

            return parent::update($id, $data);
        });
    }

    public function updateSupplierOrderDetails($order)
    {
        $arr_status = [
            // OrderConstant::KEY_STATUS_WAITING_QUOTE, // Chờ báo giá
            // OrderConstant::KEY_STATUS_WAITING_DEPOSIT, // Chờ đặt cọc
            OrderConstant::KEY_STATUS_DEPOSITED, //Đã đặt cọc
        ];
        if (in_array($order->status, $arr_status)) {
            $order->order_cost_old = $order->order_cost;
            $order->china_shipping_cost_old = $order->china_shipping_cost;
            $order->save();
        }
    }

    /**
     * @param  string  $orderId
     * @param  array  $removes
     */
    private function updateOrderRemoves(string $orderId, array $removes)
    {
        $this->prepareOrderRemoves($orderId, $removes, 'suppliers');
        $this->prepareOrderRemoves($orderId, $removes, 'products');
    }

    /**
     * @param  array  $data
     */
    private function updateOrderModifies(string $orderId, array $data): void
    {
        $this->prepareUpdateOrderModifies($orderId, OrderSupplier::class, $data, 'suppliers');
        $this->prepareUpdateOrderModifies($orderId, OrderDetail::class, $data, 'products');
    }

    /**
     * @param  string  $orderId
     * @param  string  $model
     * @param  array  $data
     * @param  string  $key
     */
    private function prepareUpdateOrderModifies(string $orderId, string $model, array $data, string $key): void
    {
        if (isset($data[$key]) && is_array($data[$key])) {
            foreach ($data[$key] as $item) {
                $query = (new $model)::query();
                $modifies = $item['modifies'];
                if ($model == OrderSupplier::class) {
                    $condition = ['order_id' => $orderId, 'supplier_id' => $item['id']];
                    $detail = $query->where($condition)->first();
                    unset($modifies['is_tax']);
                    $orderDetails = OrderDetail::query()->where($condition)->pluck('id')->all();
                    $packageIds = OrderDetailPackage::query()->where('order_detail_id', $orderDetails)->pluck(
                        'order_package_id'
                    )->all();
                    OrderPackage::query()->whereIn('id', $packageIds)->where(
                        'status',
                        PackageConstant::STATUS_PENDING
                    )->get()->each(
                        function ($package) use ($modifies) {
                            $modifies['inspection_cost'] = (bool)$modifies['is_inspection'] ? $package->inspection_cost : 0;
                            $modifies['shock_proof_cost'] = (bool)$modifies['is_shock_proof'] ? $package->shock_proof_cost : 0;
                            $modifies['woodworking_cost'] = (bool)$modifies['is_woodworking'] ? $package->woodworking_cost : 0;
                            $package->update($modifies);
                        }
                    );
                    $modifies['china_shipping_cost'] = $item['china_shipping_cost'];
                    if (isset($item['note']['private'])) {
                        $modifies['note_private'] = $item['note']['private'];
                    }
                    $content = 'order_supplier_update';
                    $object = optional(Supplier::query()->find($item['id']))->name;
                    $this->unsetProperty($modifies, 'china_shipping_cost', $detail->china_shipping_cost);
                    $this->unsetProperty($modifies, 'is_inspection', $detail->is_inspection);
                    $this->unsetProperty($modifies, 'is_shock_proof', $detail->is_shock_proof);
                    $this->unsetProperty($modifies, 'is_woodworking', $detail->is_woodworking);
                    $this->unsetProperty($modifies, 'delivery_type', $detail->delivery_type);
                }
                if ($model == OrderDetail::class) {
                    $detail = $query->find($item['id']);
                    $modifies['amount_cny'] = AccountingHelper::getCosts(
                        (int)($modifies['quantity'] ?? $detail->quantity) * (float)($modifies['unit_price_cny'] ?? $detail->unit_price_cny)
                    );
                    $this->unsetProperty($modifies, 'quantity', $detail->quantity);
                    $this->unsetProperty($modifies, 'link', $detail->link);
                    $this->unsetProperty($modifies, 'unit_price_cny', $detail->unit_price_cny);
                    $content = 'order_products_update';
                    $object = optional($detail)->name;
                }
                (new ActivityService())->productActivity($modifies, $orderId, $object, $content);
                $detail->update($modifies);
                $this->storeNote($orderId, $item);
            }
        }
    }

    private function unsetProperty(array &$modifies, string $property, $value)
    {
        if ($modifies[$property] == $value) {
            unset($modifies[$property]);
        }
    }

    /**
     * @param  array  $removes
     * @param  string  $key
     * @param  string  $orderId
     */
    private function prepareOrderRemoves(string $orderId, array $removes, string $key): void
    {
        if (isset($removes[$key])) {
            $column = 'id';
            $values = [];
            $content = 'order_products_removes';
            if ($isSupplier = $key === 'suppliers') {
                $column = 'supplier_id';
                $values = Supplier::query()->findMany($removes[$key])->pluck('name')->all();
                $content = 'order_supplier_removes';
                OrderSupplier::query()->where(['order_id' => $orderId])->whereIn($column, $removes[$key])->delete();
            }
            OrderDetail::query()->whereIn($column, $removes[$key])->where('order_id', $orderId)->each(
                function ($item) use (&$values, $isSupplier) {
                    if (!$isSupplier) {
                        $values[] = $item->name;
                    }
                    $item->delete();
                }
            );
            if (count($values) > 0)
                (new ActivityService())->setOrderLog(
                    Order::query()->find($orderId),
                    __(
                        "activity.$content",
                        [
                            'name' => getCurrentUser()->name,
                            'values' => implode(',', $values)
                        ]
                    ),
                    $orderId
                );
            $this->orderCancelIfNotExistSupplier($orderId);
        }
    }

    /**
     * @param  string  $orderId
     */
    public function orderCancelIfNotExistSupplier(string $orderId): void
    {
        if(OrderSupplier::query()->where(['order_id' => $orderId])->count() === 0)
        {
            Order::query()->find($orderId)->update(['status' => OrderConstant::KEY_STATUS_CANCEL]);
        }
    }

    /**
     * @param  string  $orderId
     * @param  array  $item
     */
    private function storeNote(string $orderId, array $item)
    {
        if (isset($item['notes'])) {
            foreach ($item['notes'] as $key => $value) {
                if(!$value) continue;
                (new NoteService())->store(
                    OrderNote::class,
                    [
                        'id' => $orderId,
                        'column' => 'order_id',
                        'content' => $value,
                        'supplier_id' => $item['id'],
                        'is_public' => $key == 'public'
                    ]
                );
            }
        }
    }

    /**
     * @param  string  $orderId
     */
    private function updateOrderCost(string $orderId): void
    {
        $data = optional(OrderSupplier::query()->where('order_id', $orderId)->selectRaw(
            'sum(order_cost) as order_cost, sum(order_fee) as order_fee,'.
            'sum(inspection_cost) as inspection_cost, sum(discount_cost) as discount_cost,'.
            'sum(china_shipping_cost) as china_shipping_cost'
        )->groupBy(
            'order_id'
        )->first())->toArray();

        if(!$data) {
            $data = ['order_cost' => 0, 'inspection_cost' => 0, 'china_shipping_cost' => 0, 'order_fee' => 0];
        }
        Order::query()->find($orderId)->update($data);
    }

    /**
     * @param  string  $orderId
     * @param  string  $column
     */
    public function incrementByColumn(string $orderId, string $column): void
    {
        optional(Order::query()->find($orderId))->increment($column);
    }

    public function getDebtCost(array $ids): float
    {
        return Order::query()->findMany($ids)->sum('debt_cost');
    }

    /**
     * @param  Order  $order
     * @return float
     */
    public function updateOrderSupplier(Order $order): float
    {
        $orderId = $order->id;

        /** @var OrderSupplier $orderSupplier */
        $orderDetails = OrderDetail::query()->where(
            ['order_id' => $orderId]
        )->groupBy('supplier_id')->selectRaw(
            "sum(amount_cny) as amount_cny, sum(quantity) as quantity, supplier_id"
        )->get();

        $inspectionCost = 0;
        $orderCost = 0;
        $orderFee = 0;
        $orderPercent = 0;
        foreach ($orderDetails as $detail) {
            $supplierId = $detail->supplier_id;
            $orderSupplier = OrderSupplier::query()->where(
                ['order_id' => $orderId, 'supplier_id' => $supplierId]
            )->first();

            if(!$orderSupplier)
            {
                $orderSupplier = OrderSupplier::query()->create(
                    ['order_id' => $orderId, 'supplier_id' => $supplierId, 'is_inspection' => $order->is_inspection, 'is_woodworking' => $order->is_woodworking, 'is_shock_proof' => $order->is_shock_proof]
                );
            }

            $data = [];
            $data['order_cost'] = $orderCostTmp = AccountingHelper::getCosts(
                $detail->amount_cny * $order->exchange_rate
            );
            $data['order_fee'] = $orderFeeTmp = (new AccountingService())->getOrderFee($orderCostTmp, $orderPercent);
            $data['discount_cost'] = (new AccountingService())->getDiscountCost($orderFee);
            $data['is_inspection'] = $orderSupplier->is_inspection;
            $data['inspection_cost'] = $data['is_inspection'] ? (new AccountingService())->getInspectionCost(
                (int)$detail->quantity
            ) : 0;

            $orderSupplier->update($data);

            $inspectionCost += $orderSupplier->is_inspection ? $data['inspection_cost'] : 0;
            $orderCost += $orderCostTmp;
            $orderFee += $orderFeeTmp;
        }

        $order->order_cost = $orderCost;
        $order->inspection_cost = $inspectionCost;
        $order->order_fee = (new AccountingService())->getOrderFee($orderCost, $orderPercent);
        $order->order_percent = $orderPercent;
        $order->save();

        return AccountingHelper::getCosts($inspectionCost);
    }

}
