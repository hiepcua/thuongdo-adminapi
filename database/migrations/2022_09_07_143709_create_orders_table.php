<?php

use App\Constants\OrderConstant;
use App\Constants\ServiceConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->string('code');
            $table->string('code_po')->nullable()->comment('Mã đặt thàng');
            $table->decimal('order_cost', 20)->default(0)->comment('Tổng giá trị đơn hàng');
            $table->decimal('exchange_rate', 20)->default(0)->comment('Tỷ giá');
            $table->enum('delivery_type', ServiceConstant::DELIVERIES)->comment('Loại vận chuyển');
            $table->decimal('delivery_cost', 10)->default(0)->comment('Chi phí vận chuyển');
            $table->decimal('inspection_cost', 10)->default(0)->comment('Phí Kiểm hàng');
            $table->decimal('woodworking_cost', 10)->default(0)->comment('Phí Đóng gỗ');
            $table->decimal('international_shipping_cost', 20)->default(0)->comment('Phí vận chuyển quốc tế');
            $table->decimal('china_shipping_cost', 20)->default(0)->comment('Phí vận chuyển TQ từ NCC -> kho');
            $table->boolean('is_woodworking')->default(0)->comment('Đóng gỗ');
            $table->boolean('is_inspection')->default(0)->comment('Kiểm hàng');
            $table->boolean('is_shock_proof')->default(0)->comment('chống Shock');
            $table->boolean('is_deposit')->default(0)->comment('Đặt cọc');
            $table->float('deposit_percent')->default(0)->comment('% đặt cọc');
            $table->decimal('deposit_cost', 20)->default(0)->comment('Tiền đặt cọc');
            $table->float('tax_percent')->default(0)->comment('% Khai thuế');
            $table->boolean('is_tax')->default(false)->comment('Khai thuế');
            $table->float('discount_percent')->default(0)->comment('% Chiết Khấu');
            $table->decimal('discount_cost', 20)->default(0)->comment('Phí Chiết Khấu');
            $table->decimal('order_fee', 20)->default(0)->comment('Phí Đặt hàng');
            $table->float('order_percent')->default(0)->comment('% đặt hàng');
            $table->uuid('customer_id')->comment('Khách hàng');
            $table->uuid('warehouse_id')->comment('Kho nhận hàng');
            $table->uuid('customer_delivery_id')->comment('Địa chỉ nhận hàng');
            $table->uuid('delivery_id')->comment('Giao hàng');
            $table->dateTime('date_ordered')->comment('Thời gian đặt hàng');
            $table->dateTime('date_purchased')->nullable()->comment('Thời gian thanh toán');
            $table->dateTime('date_done')->nullable()->comment('Thời gian hoàn thành');
            $table->dateTime('date_quotation')->nullable()->comment('Thời gian báo giá');
            $table->integer('note_number')->default(0)->comment('Số ghi chú');
            $table->integer('packages_number')->default(0)->comment('Số kiện');
            $table->integer('complains_number')->default(0)->comment('Số khiếu nại');
            $table->integer('deliveries_number')->default(0)->comment('Số giao hàng');
            $table->float('weight')->default(0)->comment('Trọng lượng');
            $table->float('volume')->default(0)->comment('Thể tích');
            $table->uuid('supplier_id')->nullable()->comment('Nhà cung cấp');
            $table->string('note', 500)->nullable()->comment('Ghi chú cho thương đô');
            $table->uuid('staff_care_id')->nullable()->comment('Nhân viên chăm sóc');
            $table->uuid('staff_quotation_id')->nullable()->comment('Nhân viên báo giá');
            $table->uuid('staff_order_id')->nullable()->comment('Nhân viên đặt hàng');
            $table->boolean('is_website')->default(0)->comment('Đặt hàng qua website');
            $table->string('ecommerce')->nullable()->comment('Đặt hàng tại Sàn');
            $table->string('reason_cancel', 500)->nullable()->comment('Lý do hủy');
            $table->string('status', 20)->default(OrderConstant::KEY_STATUS_WAITING_QUOTE)->comment(
                'Trạng thái đơn hàng'
            );

            // Phần kế toán thêm 2 key này
            $table->decimal('order_cost_old', 20)->default(0)->nullable()->comment('Tổng giá trị đơn hàng lúc báo giá');
            $table->decimal('china_shipping_cost_old', 20)->default(0)->nullable()->comment('Phí vận chuyển TQ từ NCC -> kho lúc báo giá');
            $table->text('error_logs')->nullable()->comment('Báo lỗi từ bên kế toán chi tiết đặt hàng thực tế nhà cung cấp');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
