<?php

namespace App\Http\Controllers;

use App\Constants\OrderConstant;
use App\Http\Requests\NoteOrderDetailStoreRequest;
use App\Http\Requests\Order\OrderChangeStatusRequest;
use App\Http\Requests\OrderCancelRequest;
use App\Http\Requests\OrderChangeStaffRequest;
use App\Http\Resources\ListResource;
use App\Http\Resources\Note\NoteResource;
use App\Http\Resources\Order\ProductPaginateResource;
use App\Interfaces\Validation\UpdateValidationInterface;
use App\Models\Note;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPackage;
use App\Services\DeliveryService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller implements UpdateValidationInterface
{
    public function __construct(OrderService $service)
    {
        $this->_service = $service;
    }

    /**
     * @param  OrderChangeStaffRequest  $request
     * @param  Order  $order
     * @return JsonResponse
     */
    public function changeStaff(OrderChangeStaffRequest $request, Order $order): JsonResponse
    {
        $order->update(
            request()->only(['staff_quotation_id', 'staff_order_id', 'staff_care_id'])
        );
        return resSuccess();
    }

    /**
     * @param  OrderCancelRequest  $request
     * @param  Order  $order
     * @return JsonResponse
     */
    public function cancel(OrderCancelRequest $request, Order $order): JsonResponse
    {
        $order->status = OrderConstant::KEY_STATUS_CANCEL;
        $order->reason_cancel = request()->input('reason_cancel');
        $order->save();
        return resSuccess();
    }

    /**
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function getNotes(string $orderId): JsonResponse
    {
        return resSuccessWithinData(
            new ListResource(Note::query()->where('order_id', $orderId)->get(), NoteResource::class)
        );
    }

    /**
     * @param  NoteOrderDetailStoreRequest  $request
     * @param  string  $orderId
     * @return JsonResponse
     */
    public function addNote(NoteOrderDetailStoreRequest $request, string $orderId): JsonResponse
    {
        $params = $request->all();
        $params['user_id'] = Auth::user()->id;
        $params['order_id'] = $orderId;
        return resSuccessWithinData(new NoteResource(Note::query()->create($params)));
    }

    /**
     * @param  string  $id
     * @return JsonResponse
     */
    public function getProducts(string $id): JsonResponse
    {
        $suppliers = OrderDetail::query()->where('order_id', $id)->get()->groupBy('supplier_id');
        return resSuccessWithinData(new ProductPaginateResource($suppliers));
    }

    /**
     * @param  OrderChangeStatusRequest  $request
     * @param  Order  $order
     * @return JsonResponse
     */
    public function changeStatus(OrderChangeStatusRequest $request, Order $order): JsonResponse
    {
        $order->status = request()->input('status');
        $order->save();

        // Cập nhật snapshot
        $this->_service->updateSupplierOrderDetails($order);
        return resSuccess();
    }

    public function updateMessage(): array
    {
        return [];
    }

    public function updateRequest(string $id): array
    {
        return [
            "warehouse_id" => 'required|exists:warehouses,id',
            "customer_delivery_id" => 'required|exists:customer_deliveries,id',
            "removes.suppliers" => 'array',
            "removes.suppliers.*" => 'exists:suppliers,id',
            "removes.products" => 'array',
            "removes.products.*" => 'exists:order_details,id',
            "products" => 'array',
            "products.*.id" => 'exists:order_details,id',
            "products.*.modifies.link" => 'url',
            "products.*.modifies.unit_price_cny" => 'numeric|min:1',
            "products.*.modifies.quantity" => 'numeric|min:1',
            "suppliers" => 'array',
            "suppliers.*.id" => 'exists:suppliers,id',
            "suppliers.*.modifies.delivery_type" => 'in:'.implode(',', array_keys(OrderConstant::DELIVERIES_TEXT)),
            "suppliers.*.modifies.is_woodworking" => 'boolean',
            "suppliers.*.modifies.is_shock_proof" => 'boolean',
            "suppliers.*.modifies.is_inspection" => 'boolean',
            "suppliers.*.notes.public" => 'max:500',
            "suppliers.*.notes.private" => 'max:500',
            "suppliers.*.order_fee" => 'numeric|min:0',
            "suppliers.*.discount_cost" => 'numeric|min:0',
            "suppliers.*.inspection_cost" => 'numeric|min:0',
            "suppliers.*.china_shipping_cost" => 'numeric|min:0',
            "suppliers.*.order_cost" => 'numeric|min:0',
        ];
    }

    protected function getAttributes(): array
    {
        return [

            "removes.suppliers" => 'Nhà cung cấp',
            "removes.suppliers.*" => 'Nhà cung cấp',
            "removes.products" => 'Sản phẩm',
            "removes.products.*" => 'Sản phẩm',
            "products" => 'Sản phẩm',
            "products.*.id" => 'Sản phẩm',
            "products.*.modifies.link" => 'Đường dẫn',
            "products.*.modifies.unit_price_cny" => 'Giá',
            "products.*.modifies.quantity" => 'Số lượng',
            "suppliers" => 'Nhà cung cấp',
            "suppliers.*.id" => 'Nhà cung cấp',
            "suppliers.*.modifies.delivery_type" => 'Loại vận chuyển',
            "suppliers.*.modifies.is_woodworking" => 'Loại vận chuyển',
            "suppliers.*.modifies.is_shock_proof" => 'Chống shock',
            "suppliers.*.modifies.is_inspection" => 'Kiểm đếm',
            "suppliers.*.notes.public" => 'Ghi chú khách hàng - Thương Đô',
            "suppliers.*.notes.private" => 'Ghi chú nội bộ',
            "suppliers.*.discount_cost" => 'Giá triết khấu',
            "suppliers.*.inspection_cost" => 'Giá kiểm đếm',
            "suppliers.*.china_shipping_cost" => 'Vận chuyện nội địa TQ',
            "suppliers.*.order_cost" => 'Giá đơn hàng',
            "suppliers.*.order_fee" => 'Phí đặt hàng',
        ];
    }

    /**
     * @param  string  $packageIds
     * @return JsonResponse
     */
    public function getDebt(string $packageIds): JsonResponse
    {
        $packageIds = explode(',', $packageIds);
        return resSuccessWithinData(
            (new DeliveryService())->getDebtCostByOrder(OrderPackage::query()->findMany($packageIds))
        );
    }
}
