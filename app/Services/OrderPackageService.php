<?php


namespace App\Services;


use App\Constants\ActivityConstant;
use App\Constants\PackageConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\ConvertHelper;
use App\Helpers\StatusHelper;
use App\Http\Resources\Package\PackageDetailResource;
use App\Http\Resources\Package\PackagePaginationResource;
use App\Http\Resources\Package\PackageResource;
use App\Models\Order;
use App\Models\OrderPackage;
use App\Models\OrderPackageImage;
use App\Models\OrderPackageNote;
use App\Models\OrderPackageStatusTime;
use App\Models\ReportOrderVN;
use App\Models\ReportPackage;
use App\Models\Staff;
use App\Scopes\OrganizationScope;
use Illuminate\Http\JsonResponse;

class OrderPackageService extends BaseService
{
    protected string $_resource = PackageResource::class;
    protected string $_paginateResource = PackagePaginationResource::class;


    /**
     * @param  string  $orderId
     * @return int
     */
    public function getStatusesDone(string $orderId): int
    {
        return OrderPackage::query()->where(
            ['order_id' => $orderId, 'status' => PackageConstant::STATUS_RECEIVED_GOODS]
        )->count();
    }

    /**
     * @param  string  $key
     * @return string[]
     */
    public function getStatus(string $key): array
    {
        return StatusHelper::getInfo($key, PackageConstant::class) + ['value' => $key];
    }

    public function store(array $data): OrderPackage
    {
        $data['exchange_rate'] = getExchangeRate();

        /** @var OrderPackage $package */
        $package = parent::store($data);
        $this->changeCost($package, $update);
        $package->update($update);
        $images = [];
        foreach ($data['images'] as $image) {
            $images[] = [
                'id' => getUuid(),
                'order_package_id' => $package->id,
                'image' => $image
            ];
        }
        OrderPackageImage::query()->insert($images);
        return $package;
    }

    public function detail(string $id): JsonResponse
    {
        return resSuccessWithinData(new PackageDetailResource(OrderPackage::query()->findOrFail($id)));
    }

    public function storeActivity(OrderPackage $package)
    {

        if (($old = $package->getOriginal('status')) !== $package->status) {
            (new ActivityService())->setLog($package, $package->status, ActivityConstant::PACKAGE_STATUS);
            (new OrderPackageService())->reportOrderVN($package, $old);
            (new ReportService())->inDecrementByOrganization(ReportPackage::class, $package->status, $old);
            (new OrderPackageService())->reportStatusTime($package->id, $package->status);
        }
        $this->processingNote($package, PackageConstant::TYPE_NOTE_NOTE);
        $this->processingNote($package, PackageConstant::TYPE_NOTE_ORDER);

        $this->processingInfo($package);

        $provinceId = getProvinceX(optional($package->warehouse)->province_id);
        $this->processingWeight($package, $provinceId);
        $this->processingVolume($package, $provinceId);
    }

    private function processingInfo(OrderPackage $package)
    {
        foreach (ActivityConstant::PACKAGE_PROPERTIES as $key => $value) {
            if (($old = $package->getOriginal($key)) !== ($new = $package->{$key})) {
                if(in_array($key, array_keys(ActivityConstant::PACKAGE_MODELS))) {
                    $new = $this->getContentByModelId(ActivityConstant::PACKAGE_MODELS[$key], $new);
                    $old = $this->getContentByModelId(ActivityConstant::PACKAGE_MODELS[$key], $old);
                }
                $exchangeRate = $package->exchange_rate ?? getExchangeRate();
                if(is_numeric($new) && $key != 'package_number') {
                    $old = ConvertHelper::numericToCNY($old / $exchangeRate);
                    $new = ConvertHelper::numericToCNY($new / $exchangeRate);
                }
                (new ActivityService())->setLog(
                    $package,
                    $value,
                    ActivityConstant::PACKAGE_INFO,
                    null,
                    json_encode(['old' => $old, 'new' => $new])
                );
            }
        }
    }

    private function getContentByModelId(string $model, string $id)
    {
        return optional((new $model)::query()->withoutGlobalScope(new OrganizationScope())->find($id))->name;
    }


    /**
     * @param  OrderPackage  $package
     * @param  string  $key
     */
    private function processingNote(OrderPackage $package, string $key)
    {
        $note = $package->{$key};
        if ($package->getOriginal($key) !== $package->{$key}) {
            (new ActivityService())->setLog(
                $package,
                $note,
                ActivityConstant::PACKAGE_NOTE,
                null,
                json_encode(['type' => PackageConstant::TYPES_NOTE[$key]])
            );
            $this->storeNote($package->id, $note, $key);
        }
    }

    /**
     * @param  OrderPackage  $package
     * @param  string  $provinceId
     */
    private function processingVolume(OrderPackage $package, string $provinceId): void
    {
        $width = $package->getOriginal('width');
        $height = $package->getOriginal('height');
        $length = $package->getOriginal('length');
        if (($height !== $package->height || $length !== $package->length || $width !== $package->width) && $package->getOriginal(
                'weight'
            ) == 0) {
            $oldCost = $package->getOriginal('international_shipping_cost') ?? (new AccountingService(
                ))->getInternationShippingCost(
                    $provinceId,
                    0,
                    round($height * $width * $length, 2)
                );
            $newCost = (new AccountingService())->getInternationShippingCost(
                $provinceId,
                0,
                $package->volume
            );
            $result = $oldCost - $newCost;
            $properties = json_encode(
                [
                    "unit" => 'm3',
                    'value' => $package->volume,
                    "international_old_cost" => $oldCost,
                    "international_new_cost" => $newCost,
                    "modify" => $result
                ]
            );
            ReportOrderVN::query()->where('order_id', optional($package->order)->id)->update(
                ['international_shipping_cost' => $newCost, 'shock_proof_cost' => $package->shock_proof_cost]
            );
            (new ActivityService())->setLog(
                $package,
                ConvertHelper::numericToVND($result),
                ActivityConstant::PACKAGE_WEIGHT,
                null,
                $properties
            );
        }
    }

    /**
     * @param  OrderPackage  $package
     * @param  string  $provinceId
     */
    private function processingWeight(OrderPackage $package, string $provinceId): void
    {
        if (($old = $package->getOriginal('weight')) !== $package->weight && $package->getOriginal('height') == 0) {
            $properties = json_encode(
                [
                    "unit" => 'kg',
                    'value' => $package->weight,
                    "international_old_cost" => $oldCost = $package->getOriginal(
                            'international_shipping_cost'
                        ) ?? (new AccountingService())->getInternationShippingCost(
                            $provinceId,
                            $old ?? 0,
                            0
                        ),
                    "international_new_cost" => $newCost = (new AccountingService())->getInternationShippingCost(
                        $provinceId,
                        $package->weight,
                        0
                    ),
                    "modify" => $modify = abs($newCost - $oldCost)
                ]
            );
            ReportOrderVN::query()->where('order_id', optional($package->order)->id)->update(
                ['international_shipping_cost' => $newCost, 'shock_proof_cost' => $package->shock_proof_cost]
            );
            (new ActivityService())->setLog(
                $package,
                $package->weight,
                ActivityConstant::PACKAGE_WEIGHT,
                null,
                $properties
            );
        }
    }

    public function storeNote(string $packageId, string $note, string $type): void
    {
        OrderPackageNote::query()->create(
            [
                'order_package_id' => $packageId,
                'cause_type' => Staff::class,
                'cause_id' => getCurrentUserId(),
                'content' => $note,
                'type' => $type
            ]
        );
    }

    public function changeCost(OrderPackage $package, &$array): void
    {
        $array['weight'] = $weight = (float)($array['weight'] ?? $package->weight);
        $array['height'] = $height = (float)($array['height'] ?? $package->height);
        $array['length'] = $length = (float)($array['length'] ?? $package->length);
        $array['width'] = $width = (float)($array['width'] ?? $package->width);
        $volume = roundXPrecision($height * $width * $length);
        $isWoodworking = (bool)($array['is_woodworking'] ?? $package->is_woodworking);
        $isShockProof = (bool)($array['is_shock_proof'] ?? $package->is_shock_proof);
        $isInsurance = (bool)($array['is_insurance'] ?? $package->is_insurance);
        $accounting = new AccountingService();
        $array['inspection_cost'] = 0;
        $array['insurance_cost'] = ($isInsurance && !$package->is_order) ? AccountingHelper::getCosts(
            $accounting->getInsuranceCost(
                $package->order_cost
            )
        ) : 0;
        $array['international_shipping_cost'] = $international = AccountingHelper::getCosts(
            $accounting->getInternationShippingCost(
                getProvinceX(optional($package->warehouse)->province_id),
                $weight,
                $volume
            )
        );
        $array['discount_cost'] = AccountingHelper::getCosts($accounting->getDiscountCost($international));
        $array['woodworking_cost'] = $isWoodworking ? AccountingHelper::getCosts(
            $accounting->getWoodworkingCost($weight, $volume)
        ) : 0;
        $array['shock_proof_cost'] = $isShockProof ? AccountingHelper::getCosts(
            $accounting->getShockCost($weight) * getExchangeRateByPackage($package->id)
        ) : 0;
    }

    public function getCost($that): array
    {
        return [
            'china_shipping_cost' => $that->china_shipping_cost,
            'china_shipping_cost_cny' => $that->china_shipping_cost_cny,
            'international_shipping_cost' => $that->international_shipping_cost,
            'inspection_cost' => $that->inspection_cost,
            'insurance_cost' => $that->insurance_cost,
            'woodworking_cost' => $that->woodworking_cost,
            'shock_proof_cost' => $that->shock_proof_cost,
            'storage_cost' => $that->storage_cost,
            'discount_cost' => $that->discount_cost,
            'discount_percent' => $that->discount_percent,
            'amount' => $that->amount
        ];
    }

    /**
     * @param  array  $ids
     * @param  string|null  $delivery
     */
    public function updateDeliveryIdByIds(array $ids, ?string $delivery): array
    {
        $data['amount'] = 0;
        $data['packages'] = [];
        OrderPackage::query()->findMany($ids)->each(
            function ($orderPackage) use ($delivery, &$data) {
                $orderPackage->delivery_id = $delivery;
                $orderPackage->is_delivery = !is_null($delivery);
                $orderPackage->save();
                $data['amount'] += $orderPackage->amount;
                $data['packages'][] = $orderPackage->bill_code;
            }
        );
        return $data;
    }

    /**
     * @param  string  $deliveryId
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getListByDeliveryId(string $deliveryId)
    {
        return OrderPackage::query()->where('delivery_id', $deliveryId)->get();
    }

    /**
     * @param  OrderPackage  $package
     * @param  string  $oldStatus
     */
    public function reportOrderVN(OrderPackage $package, string $oldStatus): void
    {
        if (($decrease = $oldStatus === PackageConstant::STATUS_WAREHOUSE_VN) || ($increase = $package->status === PackageConstant::STATUS_WAREHOUSE_VN)) {
            $report = ReportOrderVN::query()->firstOrCreate(
                ['customer_id' => $package->customer_id, 'order_id' => $package->order_id]
            );
            $columns = [
                'inspection_cost',
                'insurance_cost',
                'woodworking_cost',
                'shock_proof_cost',
                'international_shipping_cost',
                'china_shipping_cost',
                'delivery_cost',
                'order_cost',
                'order_fee'
            ];
            $array = $package->only($columns);
            $dynamic = ($decrease ? -1 : 1);
            foreach ($array as $key => $value) {
                $report->{$key} += (float)$value * $dynamic;
            }
            $deposit = optional($package->order)->deposit_cost ?? 0;
            $depositPercent = optional($package->order)->percent ?? 0;
            $report->order_type = get_class($package->order);
            $report->order_cost = $orderCost = ($package->order_type === Order::class ? $package->order_cost : 0);
            $deposit = $deposit > $orderCost ? AccountingHelper::getCosts($orderCost * $depositPercent / 100) : $deposit;
            $report->deposit_cost = $package->is_order ? $deposit : 0;
            $report->order_fee = $package->order_fee;
            $report->save();
        }
    }

    public function reportStatusTime(string $id, string $status)
    {
        OrderPackageStatusTime::query()->create(
            ['key' => $status, 'order_package_id' => $id]
        );
    }

    /**
     * @param  string  $orderId
     * @return bool
     */
    public function checkPackageIsLatestByOrder(string $orderId, array $packages): bool
    {
        $results = OrderPackage::query()->where('order_id', $orderId)->orderBy('id')->get();
        $last = $results->last();
        $process = $results->whereIn('id', $packages)->all();
        return $last->id === array_pop($process)['id'] ?? '';
    }

}