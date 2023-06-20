<?php

namespace App\Http\Controllers;

use App\Constants\PackageConstant;
use App\Constants\ReportConstant;
use App\Helpers\AccountingHelper;
use App\Helpers\RandomHelper;
use App\Http\Requests\Package\ChangeStatusRequest;
use App\Http\Requests\Package\PackageModifiesRequest;
use App\Http\Requests\Package\PackageStoreRequest;
use App\Http\Requests\Package\PackageUpdateRequest;
use App\Http\Resources\ListResource;
use App\Http\Resources\Package\PackageStoreByOrderResource;
use App\Http\Resources\Package\ProductResource;
use App\Interfaces\Validation\StoreValidationInterface;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Models\OrderSupplier;
use App\Services\OrderPackageService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller implements StoreValidationInterface
{
    public function __construct(OrderPackageService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getListByOrderId(string $orderId): JsonResponse
    {
        return resSuccessWithinData(new ListResource(OrderPackage::query()->where('order_id', $orderId)->get(), PackageStoreByOrderResource::class));
    }

    /**
     * @param  PackageStoreRequest  $request
     * @param  Order  $order
     * @return JsonResponse
     */
    public function storeByOrderId(PackageStoreRequest $request, Order $order): JsonResponse
    {
        $package = DB::transaction(function() use($request, $order)  {
            $params = $request->all();
            $detail = OrderDetail::query()->find($params['products'][0]['id']);
            $orderSupplier = OrderSupplier::query()->where(['order_id' => $order->id, 'supplier_id' => $detail->supplier_id])->first();
            $params['order_type'] = get_class($order);
            $params['order_id'] = $order->id;
            $params['exchange_rate'] = getExchangeRate();
            $params['staff_order_id'] = $order->staff_order_id;
            $params['staff_quotation_id'] = $order->staff_quotation_id;
            $params['staff_care_id'] = $order->staff_care_id;
            $params['order_code'] = $order->code;
            $params['quantity'] = array_sum(Arr::pluck($params['products'], 'quantity'));
            $params['order_cost'] = OrderDetail::query()->findMany(Arr::pluck($params['products'], 'id'))->sum('unit_price_cny') *  $params['quantity'] * $order->exchange_rate;
            $params['customer_id'] = $order->customer_id;
            $params['warehouse_id'] = $order->warehouse_id;
            $params['customer_delivery_id'] = $order->customer_delivery_id;
            $params['ecommerce'] = $order->ecommerce;
            $params['is_inspection'] = $orderSupplier->is_inspection ?? $order->is_inspection;
            $params['is_woodworking'] = $orderSupplier->is_woodworking ?? $order->is_woodworking;
            $params['is_shock_proof'] = $orderSupplier->is_shock_proof ?? $order->is_shock_proof;
            $params['is_extension'] = !!$order->ecommerce;
            
            $params['code'] = RandomHelper::getPackageCode();
            /** @var OrderPackage $package */
            $package = OrderPackage::query()->create($params);
            
            $this->_service->changeCost($package, $modify);
            $data = [];
            foreach ($params['products'] as $value) {
                $data[$value['id']] = ['quantity' => $value['quantity']];
            }
            $package->orderDetails()->sync($data);
            $package->update($modify);
            return $package;
        });
        return resSuccessWithinData(new PackageStoreByOrderResource($package));
    }

    /**
     * @param  PackageUpdateRequest  $request
     * @param  OrderPackage  $package
     * @return JsonResponse
     */
    public function updateByOrderId(PackageUpdateRequest $request, OrderPackage $package): JsonResponse
    {
        $params = $request->all();
        $params['staff_id'] = getCurrentUserId();
        $params['code'] = RandomHelper::getPackageCode();
        $this->_service->changeCost($package, $params);
        $package->update($params);
        $data = [];
        foreach ($params['products'] as $value) {
            $data[$value['id']] = ['quantity' => $value['quantity']];
        }
        $package->orderDetails()->sync($data);
        return resSuccessWithinData(new PackageStoreByOrderResource($package));
    }

    public function storeMessage(): ?array
    {
        return [];
    }

    public function storeRequest(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'bill_code' => 'required|unique:order_package',
            'type' => 'required|in:' . implode(',', array_keys(PackageConstant::TYPES)),
            'weight' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'staff_order_id' => 'nullable|exists:users,id',
            'order_code' => 'nullable|exists:orders,code',
            'transporter_id' => 'required|exists:transporters,id',
            'category_id' => 'required|exists:categories,id',
            'product_name' => 'required|max:255',
            'china_shipping_cost' => 'nullable|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id'
        ];
    }

    protected function getAttributes(): array
    {
        return [
            'customer_id' => 'Khách hàng',
            'bill_code' => 'Mã vận đơn',
            'type' => 'Loại kiện',
            'weight' => 'Cân nặng',
            'width' => 'Chiều rộng',
            'height' => 'Chiều cao',
            'length' => 'Chiều dài',
            'staff_order_id' => 'Nhân viên đặt hàng',
            'order_code' => 'Mã đơn hàng',
            'transporter_id' => 'Hãng vận chuyển',
            'category_id' => 'Danh mục',
            'product_name' => 'Tên sản phẩm',
            'china_shipping_cost' => 'Phí vận chuyển TQ',
            'warehouse_id' => 'Kho nhận',
            'images' => 'array'
        ];
    }

    /**
     * @param  PackageModifiesRequest  $request
     * @param  OrderPackage  $package
     * @return JsonResponse
     */
    public function modifies(PackageModifiesRequest $request, OrderPackage $package): JsonResponse
    {
        $params = $request->all();
        if (isset($params['china_shipping_cost_cny'])) {
            $params['china_shipping_cost'] = AccountingHelper::getCosts(
                $params['china_shipping_cost_cny'] * getExchangeRate()
            );
        }

        $this->_service->changeCost($package, $params);

        if (isset($params['type']) && $params['type'] == 'weight' && $package->weight != $params['weight']) {
            (new ReportService())->decrementByReportRevenue(
                $package->volume,
                ReportConstant::SHIPPING_M3
            );
            (new ReportService())->incrementByReportRevenue($params['weight'], ReportConstant::SHIPPING_KG);
            $params['height'] = 0;
            $params['width'] = 0;
            $params['length'] = 0;
        }
        if (isset($params['type']) && $params['type'] == 'volume' && ($package->height != $params['height'] || $package->length != $params['length'] || $package->width != $params['width'])) {
            (new ReportService())->incrementByReportRevenue(
                (float)$params['height'] * (float)$params['width'] * (float)$params['length'],
                ReportConstant::SHIPPING_M3
            );
            (new ReportService())->decrementByReportRevenue($package->weight, ReportConstant::SHIPPING_KG);
            $params['weight'] = 0;
        }

        $package->update($params);
        return resSuccessWithinData($package);
    }

    /**
     * @param  OrderPackage  $package
     * @return JsonResponse
     */
    public function getProducts(OrderPackage $package): JsonResponse
    {
        $suppliers = $package->products->groupBy('supplier_id');
        return resSuccessWithinData(new ProductResource($suppliers));
    }

    /**
     * @param  string  $customerId
     * @return JsonResponse
     */
    public function getListVNByCustomerId(string $customerId): JsonResponse
    {
        return resSuccessWithinData(
            OrderPackage::query()->where('customer_id', $customerId)->whereNull('delivery_id')->where(
                'status',
                PackageConstant::STATUS_WAREHOUSE_VN
            )->select('id', 'bill_code', 'order_id')->get()
        );
    }

    /**
     * @param  ChangeStatusRequest  $request
     * @return JsonResponse
     */
    public function changeStatus(ChangeStatusRequest $request): JsonResponse
    {
        OrderPackage::query()->findMany($request->packages)->each(
            function ($package) {
                $package->status = request()->input('status');
                $package->save();
            }
        );
        return resSuccess();
    }

    /**
     * @param  string  $ids
     * @return JsonResponse
     */
    public function getChinaCost(string $ids): JsonResponse
    {
        $ids = explode(',', $ids);
        return resSuccessWithinData(OrderPackage::query()->findMany($ids)->sum('china_shipping_cost'));
    }
}
