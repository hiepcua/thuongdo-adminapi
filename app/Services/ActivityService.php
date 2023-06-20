<?php


namespace App\Services;


use App\Constants\ActivityConstant;
use App\Constants\OrderConstant;
use App\Helpers\TimeHelper;
use App\Http\Resources\Activity\ActivityPackageResource;
use App\Http\Resources\Activity\ActivityResource;
use App\Http\Resources\ListResource;
use App\Http\Resources\ReportStatusResource;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityService extends BaseService
{
    protected string $_resource = ActivityResource::class;

    /**
     * @param  int  $perPage
     * @return JsonResponse
     * @throws \Exception
     */
    public function pagination(int $perPage): JsonResponse
    {
        return parent::pagination($perPage);
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $orderId
     */
    public function setOrderLog(Model $subject, string $content, string $orderId)
    {
        $data = new ReportStatusResource(
            Order::query()->find($orderId)->status, OrderConstant::STATUSES, OrderConstant::STATUSES_COLOR
        );
        $this->setLog($subject, $content, ActivityConstant::ORDER_LOG, $orderId, json_encode($data));
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $logName
     * @param  string|null  $objectId
     * @param  string|null  $properties
     */
    public function setLog(Model $subject, string $content, string $logName, ?string $objectId = null, ?string $properties = null)
    {
        /** @var Customer $user */
        $user = Auth::user() ?? User::query()->withoutGlobalScope(
                OrganizationScope::class
            )->first();
        Activity::query()->create(
            [
                'subject_type' => get_class($subject),
                'subject_id' => $subject->id,
                'causer_type' => get_class($user),
                'causer_id' => $user->id,
                'log_name' => $logName,
                'content' => $content,
                'object_id' => $objectId,
                'organization_id' => $user->organization_id ?? getOrganization(),
                'properties' => $properties,
            ]
        );
    }

    /**\
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getOrderLog(string $orderId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::ORDER_LOG, 'object_id' => $orderId]
        )->get();
        return resSuccessWithinData(
            $activities->transform(
                function ($item) {
                    return [
                        'time' => TimeHelper::format($item->created_at),
                        'label' => json_decode($item->properties) ?? ['name' => $item->subject->getTableFriendly(), 'color' => $item->subject->getColorLog()],
                        'content' => sprintf($item->content, optional($item->causer)->name)
                    ];
                }
            )
        );
    }

    /**
     * @param  string  $packageId
     * @return JsonResponse
     */
    public function getPackageLog(string $packageId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_LOG, 'subject_id' => $packageId]
        )->get();
        return resSuccessWithinData(new ListResource($activities, ActivityPackageResource::class));
    }

    public function getPackageDetailLog(string $packageId): JsonResponse
    {
        $data['note'] = new ListResource(Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_NOTE, 'subject_id' => $packageId]
        )->get(), ActivityPackageResource::class);
        $data['status'] = new ListResource(Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_STATUS, 'subject_id' => $packageId]
        )->get(), ActivityPackageResource::class);
        $data['weight'] = new ListResource(Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_WEIGHT, 'subject_id' => $packageId]
        )->get(), ActivityPackageResource::class);
        $data['info'] = new ListResource(Activity::query()->where(
            ['log_name' => ActivityConstant::PACKAGE_INFO, 'subject_id' => $packageId]
        )->get(), ActivityPackageResource::class);
        return resSuccessWithinData($data);
    }

    /**
     * @param  string  $consignmentId
     * @return JsonResponse
     */
    public function getConsignmentLog(string $consignmentId): JsonResponse
    {
        $activities = Activity::query()->where(
            ['log_name' => ActivityConstant::CONSIGNMENT_LOG, 'object_id' => $consignmentId]
        )->get();
        return resSuccessWithinData(
            $activities->transform(
                function ($item) {
                    return new $this->_resource($item);
                }
            )
        );
    }

    public function getDeliveryLog(string $deliveryId): JsonResponse
    {
        $data['statuses'] = new ListResource(
            Activity::query()->where(
                ['log_name' => ActivityConstant::DELIVERY_STATUS, 'subject_id' => $deliveryId]
            )->get(), ActivityPackageResource::class
        );

        $data['packages'] = new ListResource(
            Activity::query()->where(
                ['log_name' => ActivityConstant::DELIVERY_PACKAGE, 'subject_id' => $deliveryId]
            )->get(), ActivityPackageResource::class
        );

        return resSuccessWithinData($data);
    }

    /**
     * @param  Model  $subject
     * @param  string  $content
     * @param  string  $orderId
     */
    public function setConsignmentLog(Model $subject, string $content, string $orderId)
    {
        $this->setLog($subject, $content, ActivityConstant::CONSIGNMENT_LOG, $orderId);
    }

    /**
     * @param  Order  $order
     */
    public function activitiesOrder(Order $order)
    {
        $this->prepareActivityOrder(
            $order,
            'customer_delivery_id',
            'activity.order_customer_delivery',
            optional($order->customerDelivery)->custom_name
        );
        $this->prepareActivityOrder(
            $order,
            'warehouse_id',
            'activity.order_warehouse',
            optional($order->warehouse)->custom_name
        );
    }

    /**
     * @param  Order  $order
     * @param  string  $column
     * @param  string  $content
     * @param  string  $value
     */
    private function prepareActivityOrder(Order $order, string $column, string $content, string $value)
    {
        $activity = new ActivityService();
        if ($order->getOriginal($column) != $order->{$column}) {
            $activity->setOrderLog(
                $order,
                __($content, ['name' => getCurrentUser()->name, 'value' => $value]),
                $order->id
            );
        }
    }

    public function productActivity(array $modifies, string $orderId, string $objects, string $content)
    {
        $array = [
            'link' => 'link sản phẩm',
            'unit_price_cny' => 'Đơn giá (CNY)',
            'quantity' => 'Số lượng',
            'china_shipping_cost' => 'Ship nội địa TQ',
            'delivery_type' => 'Loại vận chuyển',
            'is_woodworking' => 'Đóng gỗ',
            'is_shock_proof' => 'Chống sốc',
            'is_inspection' => 'Kiểm đếm',
            'note_private' => 'Ghi chú nội bộ'
        ];
        foreach ($modifies as $field => $modify) {
            if (!isset($array[$field])) {
                continue;
            }
            $value = $modify;
            if (Str::startsWith($field, 'is_')) {
                $value = $modify == 1 ? 'Có' : 'Không';
            }
            if (Str::startsWith($field, 'delivery_type')) {
                $value = OrderConstant::DELIVERIES_TEXT[$modify];
            }
            (new ActivityService())->setOrderLog(
                Order::query()->find($orderId),
                __(
                    'activity.'.$content,
                    [
                        'object' => $objects,
                        'name' => getCurrentUser()->name,
                        'field' => $array[$field],
                        'value' => $value
                    ]
                ),
                $orderId
            );
        }
    }
}