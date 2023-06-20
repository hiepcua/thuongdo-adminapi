<?php

use App\Constants\ServiceConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_supplier', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('supplier_id');
            $table->decimal('order_cost', 20);
            $table->decimal('order_fee', 20)->default(0)->comment('Phí Đặt hàng');
            $table->decimal('discount_cost', 20)->default(0)->comment('Phí Triết khấu');
            $table->decimal('inspection_cost', 20)->default(0)->comment('Phí Kiểm hàng');
            $table->decimal('shock_proof_cost', 20)->default(0)->comment('Phí chống sốc');
            $table->boolean('is_shock_proof')->default(false)->comment('Chống sốc');
            $table->boolean('is_woodworking')->default(0)->comment('Đóng gỗ');
            $table->boolean('is_inspection')->default(0)->comment('Kiểm hàng');
            $table->decimal('china_shipping_cost', 20)->default(0)->comment('Phí vận chuyển TQ từ NCC -> kho');
            $table->decimal('international_shipping_cost', 20)->default(0)->comment('Phí vận chuyển Quốc tế');
            $table->enum('delivery_type', ServiceConstant::DELIVERIES)->comment('Loại vận chuyển');
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
        Schema::dropIfExists('order_supplier');
    }
}
