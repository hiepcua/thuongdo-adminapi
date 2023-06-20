<?php


namespace App\Services;

use App\Constants\TransactionConstant;
use App\Helpers\ConvertHelper;
use App\Helpers\RandomHelper;
use App\Models\Consignment;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Transaction;
use App\Scopes\OrganizationScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionService implements Service
{
    private ReportCustomerService $_reportCustomer;

    public function __construct()
    {
        $this->_reportCustomer = new ReportCustomerService();
    }

    /**
     * @param  float  $amount
     * @param  string  $status
     * @param  string  $content
     * @param  bool  $isIncrement
     * @param  string  $customerId
     * @param  null  $sourceable
     */
    public function setTransaction(
        float $amount,
        string $status,
        string $content,
        bool $isIncrement,
        string $customerId,
        $sourceable = null
    ) {
        $balance = (new CustomerService())->getBalanceAmount($customerId);
        if ($isIncrement) {
            $result = $balance + $amount;
            $this->_reportCustomer->balanceAmountIncrease($amount, $customerId);
        } else {
            $result = $balance - $amount;
            if($result <= 0) {
                $amount = $balance;
                $result = 0;
            }
            $this->_reportCustomer->balanceAmountDecrease($amount, $customerId);
        }
        Transaction::query()->create(
            [
                'customer_id' => $customerId,
                'sourceable_type' => $sourceable ? get_class($sourceable) : null,
                'sourceable_id' => $sourceable ? $sourceable->id : null,
                'amount' => $amount,
                'time' => now(),
                'status' => $status,
                'content' => $content,
                'code' => RandomHelper::getTransactionCode(),
                'balance' => $result,
                'organization_id' => getOrganization()
            ]
        );
    }

    /**
     * @param  float  $amount
     * @param  string  $status
     * @param  string  $content
     * @param  string  $customerId
     * @param  null  $sourceable
     */
    public function setTransactionIncrement(
        float $amount,
        string $status,
        string $content,
        string $customerId,
        $sourceable = null
    ) {
        $this->setTransaction($amount, $status, $content, true, $customerId, $sourceable);
    }

    /**
     * @param  float  $amount
     * @param  string  $status
     * @param  string  $content
     * @param  string  $customerId
     * @param  null  $sourceable
     */
    public function setTransactionDecrement(
        float $amount,
        string $status,
        string $content,
        string $customerId,
        $sourceable = null
    ) {
        $this->setTransaction($amount, $status, $content, false, $customerId, $sourceable);
    }

    /**
     * @param  Delivery  $delivery
     * @param  array  $params
     */
    public function purchaseDelivery(Delivery $delivery, array $params): void
    {
        (new CustomerService())->getBalanceAmount($delivery->customer_id);
        DB::transaction(
            function () use ($delivery, $params) {
                $amount = $delivery->amount;
                if ($amount === 0) {
                    return;
                }
                $this->setDeliveryTransaction($delivery);
                $this->_reportCustomer->increaseOrderAmount($delivery->customer_id, $delivery->debt_cost);
                $this->_reportCustomer->updateLevel($delivery->customer_id);
            }
        );
    }

    /**
     * @param  Delivery  $delivery
     */
    public function setDeliveryTransaction(Delivery $delivery): void
    {
        $array = [
            'debt_cost' => 'delivery_debt_cost',
            'shipping_cost' => 'delivery_shipping_cost',
            'delivery_cost' => 'delivery_cost'
        ];
        $packages = optional($delivery->packages);
        $ordersPackage = optional($packages)->where('order_type', Order::class);
        $consignmentsPackage = optional($packages)->where('order_type', Consignment::class);
        $ordersCode = $this->getValuesByArrayAndColumn($ordersPackage, 'order_code');
        $consignmentsCode = $this->getValuesByArrayAndColumn($consignmentsPackage, 'order_code');
        foreach ($array as $key => $msg) {
            $amount = $delivery->{$key};
            if ($amount <= 0) {
                continue;
            }

            if ($key === 'shipping_cost') {
                $orders = $ordersCode;
            } else {
                $orders = array_unique(array_merge($ordersCode, $consignmentsCode));
            }

            $params = [
                'amount' => ConvertHelper::numericToVND($amount),
            ];

            if ($key !== 'debt_cost') {
                $params['bill'] = implode(
                    ',',
                    $this->getValuesByArrayAndColumn(
                        $key == 'shipping_cost' ? $ordersPackage : $packages,
                        'bill_code'
                    )
                );
            } else {
                $orders = $this->getOrders($ordersCode);
            }

            $params['code'] = implode(',', $orders);

            $msg = trans(
                "transaction.$msg",
                $params
            );
            $this->setTransaction(
                $amount,
                TransactionConstant::STATUS_PURCHASE,
                $msg,
                false,
                $delivery->customer_id,
                $delivery
            );
        }
    }

    /**
     * @param $object
     * @param  string  $column
     * @return array
     */
    private function getValuesByArrayAndColumn($object, string $column): array
    {
        return optional($object)->pluck($column)->all();
    }

    /**
     * @param  array  $orders
     * @return array
     */
    private function getOrders(array $orders): array
    {
        return Order::query()->whereIn('code', $orders)->whereRaw('order_cost > deposit_cost')->get()->pluck(
            'code'
        )->all();
    }
}