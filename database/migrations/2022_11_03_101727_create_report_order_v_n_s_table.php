<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportOrderVNSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_orders_vn', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuidMorphs('order');
            $table->decimal('order_cost', 20)->default(0)->comment('Tiền hàng');
            $table->decimal('order_fee', 20)->default(0)->comment('Phí đặt hàng');
            $table->decimal('deposit_cost', 20)->default(0)->comment('Tiền đặt cọc');
            $table->decimal('inspection_cost', 10)->default(0)->comment('Phí Kiểm hàng');
            $table->decimal('insurance_cost', 20)->default(0)->comment('Bảo hiểm');
            $table->decimal('woodworking_cost', 10)->default(0)->comment('Phí Đóng gỗ');
            $table->decimal('storage_cost', 20)->default(0)->comment('Lưu kho');
            $table->decimal('shock_proof_cost', 20)->default(0)->comment('Phí vận chuyển quốc tế');
            $table->decimal('international_shipping_cost', 20)->default(0)->comment('Phí vận chuyển quốc tế');
            $table->decimal('china_shipping_cost', 20)->default(0)->comment('Phí vận chuyển TQ từ NCC -> kho');
            $table->decimal('delivery_cost', 20)->default(0)->comment('Nội địa VN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_orders_vn');
    }
}
