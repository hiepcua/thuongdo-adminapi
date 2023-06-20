<?php


namespace App\Services;


use App\Constants\DeliveryConstant;
use App\Constants\NoteConstant;
use App\Constants\TransactionConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\ConvertHelper;
use App\Helpers\RandomHelper;
use App\Http\Resources\Delivery\DeliveryResource;
use App\Models\CustomerDelivery;
use App\Models\Delivery;
use App\Models\DeliveryNote;
use App\Models\DeliveryOrder;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DeliveryService extends BaseService
{
    protected string $_resource = DeliveryResource::class;
    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return Delivery::query()->join('orders', 'orders.delivery_id', '=', 'deliveries.id')->where(
            ['orders.id' => $orderId, 'deliveries.status' => DeliveryConstant::KEY_STATUS_DONE]
        )->count();
    }

    public function store(array $data)
    {
        $fees = [];
        $checkWallet = $this->checkWallet($fees, $data['packages'], $data['customer_delivery_id']);
        $data += $fees;
        return DB::transaction(
            function () use ($data, $checkWallet) {
                $packages = $data['packages'];
                $delivery = $this->storeByOrder($data);
                if ($data['payment'] === DeliveryConstant::PAYMENT_E_WALLET) {
                    enoughMoneyToPay($checkWallet);
                    (new TransactionService())->purchaseDelivery($delivery, $packages);
                }
                return $delivery;
            }
        );
    }

    /**
     * @param  array  $data
     * @return Delivery
     */
    public function storeByOrder(array $data): Delivery
    {
        /** @var CustomerDelivery $customDelivery */
        $customDelivery = CustomerDelivery::query()->select(
            'receiver',
            'address',
            'phone_number',
            'delivery_cost',
            'province_id',
            'district_id',
            'ward_id',
            'customer_id'
        )->findOrFail(
            $data['customer_delivery_id']
        )->toArray();
        $customDelivery['address'] = $customDelivery['address_only'];
        $data += $customDelivery;

        $packages = OrderPackage::query()->find($data['packages']);
        $order = optional($packages->first())->order;
        $data['code'] = RandomHelper::getDeliveryCode();
        $data['is_delivery_cost_paid'] = $data['delivery_cost'] > 0;
        $data['shock_proof_cost'] = $packages->sum('shock_proof_cost');
        $data['storage_cost'] = $packages->sum('storage_cost');
        $data['woodworking_cost'] = $packages->sum('woodworking_cost');
        $data['inspection_cost'] = $packages->sum('inspection_cost');
        $data['insurance_cost'] = $packages->sum('insurance_cost');
        $data['international_shipping_cost'] = $packages->sum('international_shipping_cost');
        $data['china_shipping_cost'] = $packages->sum('china_shipping_cost');
        $data['discount_cost'] = $packages->sum('discount_cost');
        $data['organization_id'] = getOrganization();
        $data['order_id'] = optional($order)->id;
        $data['order_type'] = get_class($order);
        /** @var Delivery $delivery */
        $delivery = Delivery::query()->create($data);
        foreach ($packages as $package) {
            DeliveryOrder::query()->create(
                [
                    'delivery_id' => $delivery->id,
                    'order_type' => $package->order_type,
                    'order_id' =>  $package->order_id,
                ]
            );
        }
        OrderPackage::query()->findMany($data['packages'])->each(
            function ($item) use ($delivery) {
                $item->update(
                    ['delivery_id' => $delivery->id, 'is_delivery' => true]
                );
            }
        );

        if (isset($data['note']) && $data['note']) {
            $this->storeNote($delivery->id, $data['note']);
        }
        
        $this->setLogs($delivery, $packages);
        return $delivery;
    }

    /**
     * @param  array  $params
     * @param  array  $packages
     * @param  string  $customerDeliveryId
     * @return bool
     */
    private function checkWallet(array &$params, array $packages, string $customerDeliveryId): bool
    {
        $packages = OrderPackage::query()->findMany($packages);
        $customerDelivery = CustomerDelivery::query()->findOrFail($customerDeliveryId);
        $delivery = optional($customerDelivery)->delivery_cost ?? 0;
        $debt = $this->getDebtCostByOrder($packages);
        $shipping = $packages->sum('shipping_cost');
        $params = ['delivery_cost' => $delivery, 'debt_cost' => $debt, 'shipping_cost' => $shipping];
        return (new CustomerService())->getBalanceAmount(
                $customerDelivery->customer_id
            ) < ($delivery + $debt + $shipping);
    }

    /**
     * @param  Collection  $packages
     * @return float
     */
    public function getDebtCostByOrder(Collection $packages): float
    {
        $debt = 0;
        $orderIds = $packages->pluck('order_id')->all();
        $packages = $packages->pluck('id')->all();
        foreach (array_unique($orderIds) as $orderId) {
            if (!OrderPackage::query()->where(['order_id' => $orderId])->whereNotIn('id', $packages)->whereNull('delivery_id')->exists()) {
                $debt += (new OrderService())->getDebtCost([$orderId]);
            }
        }
        return $debt;
    }

    /**
     * @param  Delivery  $delivery
     * @param $packages
     */
    private function setLogs(Delivery $delivery, $packages)
    {
        foreach ($packages as $package) {
            $order = $package->order;
            $order->update(['delivery_id' => $delivery->id]);
            if ($order instanceof Order) {
                (new ActivityService())->setOrderLog($delivery, trans("activity.order_delivery"), $order->id);
                (new OrderService())->incrementByColumn($order->id, 'deliveries_number');
                continue;
            }

            (new ActivityService())->setConsignmentLog($delivery, trans("activity.order_delivery"), $order->id);
            (new ConsignmentService())->incrementByColumn($order->id, 'deliveries_number');
        }
    }

    public function update(string $id, array $data)
    {
        $package = new OrderPackageService();

        $delivery = Delivery::query()->find($id);

        if (isset($data['payment']) && $data['payment'] === $delivery->payment && $delivery->payment !== DeliveryConstant::PAYMENT_E_WALLET) {
            return resSuccessWithinData(new DeliveryResource(parent::update($id, $data)));
        }
        $dataRefund = json_decode($delivery->refund, true) ?? ['amount' => 0, 'bill_code' => []];
        $package->updateDeliveryIdByIds($data['packages']['add'] ?? [], $id);
        $package->updateDeliveryIdByIds($data['packages']['remove'] ?? [], null);
        $deliveryNewCost = optional($delivery->customerDelivery)->delivery_cost ?? 0;
        $deliveryOldCost = $delivery->delivery_cost ?? 0;
        $amount = $deliveryOldCost - $deliveryNewCost;
        if (isset($data['payment']) && $data['payment'] === $delivery->payment && $delivery->payment === DeliveryConstant::PAYMENT_E_WALLET) {
            if ($amount > 0) {
                $dataRefund['amount'] += $amount;
            } else {
                $this->purchaseDelivery($delivery, $deliveryOldCost, $deliveryNewCost);
            }

        }

        if (isset($data['payment']) && $data['payment'] !== $delivery->payment && $delivery->payment !== DeliveryConstant::PAYMENT_E_WALLET) {
            $this->purchaseDelivery($delivery, 0, $delivery->amount);
        }

        if (isset($data['payment']) && $data['payment'] !== $delivery->payment && $delivery->payment === DeliveryConstant::PAYMENT_E_WALLET) {
            $dataRefund['amount'] += $delivery->amount - $amount;
        }
        $dataRefund['bill_code'] = $delivery->packages->pluck('bill_code')->all();
        if(isset($data['payment']) && $data['payment'] === DeliveryConstant::PAYMENT_E_WALLET) {
            $dataRefund['amount'] = 0;
        }
        $data['refund'] = json_encode($dataRefund);
        $data['delivery_cost'] = $deliveryNewCost;

        return new DeliveryResource(parent::update($id, $data));
    }

    /**
     * @param  Delivery  $delivery
     * @param  float  $amountOld
     * @param  float  $amountNew
     */
    public function purchaseDelivery(Delivery $delivery, float $amountOld, float $amountNew)
    {
        (new TransactionService())->setTransactionDecrement(
            AccountingHelper::getCosts($amountNew - $amountOld),
            TransactionConstant::STATUS_PURCHASE,
            __(
                'transaction.delivery_change_deliver_cost',
                [
                    'name' => getCurrentUser()->name,
                    'amountOld' => ConvertHelper::numericToVND($amountOld),
                    'amountNew' => ConvertHelper::numericToVND($amountNew)
                ]
            ),
            $delivery->customer_id,
            $delivery
        );
    }

    /**
     * @param  Delivery  $delivery
     * @param  float  $amount
     * @param  string  $content
     */
    public function refundDelivery(Delivery $delivery, float $amount, string $content)
    {
        (new TransactionService())->setTransactionIncrement(
            $amount,
            TransactionConstant::STATUS_REFUND,
            $content,
            $delivery->customer_id,
            $delivery
        );
    }

    public function destroy(string $id): JsonResponse
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::query()->findOrFail($id);
        $this->refundDelivery($delivery, $delivery->amount, __('transaction.delivery_destroy', ['amount' => ConvertHelper::numericToVND($delivery->amount), 'bill' => implode(',', $delivery->orderPackages->pluck('bill_code')->all()), 'name' => getCurrentUser()->name]));
        $delivery->orderPackages()->update(['delivery_id' => null, 'is_delivery' => false]);
        return parent::destroy($id);
    }

    public function storeNote(string $deliveryId, string $content)
    {
        DeliveryNote::query()->create(
            [
                'delivery_id' => $deliveryId,
                'content' => $content,
                'cause_id' => getCurrentUserId(),
                'cause_type' => Staff::class,
                'type' => NoteConstant::TYPE_PRIVATE
            ]
        );
    }
}