<?php

use App\Constants\DeliveryConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuidMorphs('order');
            $table->uuid('customer_id');
            $table->string('code');
            $table->string('receiver');
            $table->string('phone_number');
            $table->string('address');
            $table->date('date')->nullable()->comment('Ngày giao');
            $table->uuid('transporter_id')->comment('Hình thức vận chuyển');
            $table->uuid('transporter_detail_id')->nullable()->comment('Xe vận chuyển');
            $table->decimal('delivery_cost', 20)->default(0)->comment('Tiền vận chuyển');
            $table->boolean('is_delivery_cost_paid')->nullable()->comment(
                'Phí vận chuyển giao hàng đã được thanh toán?'
            );
            $table->decimal('debt_cost', 20)->default(0)->comment('Dư nợ tiền hàng');
            $table->decimal('international_shipping_cost', 20)->default(0)->comment('Dư nợ ship QT');
            $table->decimal('china_shipping_cost', 20)->default(0)->comment('Dư nợ ship china');
            $table->decimal('shock_proof_cost', 20)->default(0)->comment('Phí chống sốc');
            $table->decimal('storage_cost', 20)->default(0)->comment('Phí lưu kho');
            $table->decimal('woodworking_cost', 10)->default(0)->comment('Phí Đóng gỗ');
            $table->decimal('inspection_cost', 10)->default(0)->comment('Phí Kiểm hàng');
            $table->decimal('insurance_cost', 10)->default(0)->comment('Phí Kiểm hàng');
            $table->decimal('discount_cost', 10)->default(0)->comment('Chiết khấu');
            $table->longText('refund')->nullable()->comment('Hoàn lại');
            $table->enum('type', ['normal', 'fast'])->default('normal')->comment('Phương thức vận chuyển');
            $table->enum('payment', ['e-wallet', 'cod'])->default('e-wallet')->comment('Hình thức thanh toán');
            $table->string('note', 500)->nullable()->comment('Ghi chú');
            $table->string('note_customer', 500)->nullable()->comment('Ghi chú của khách hàng');
            $table->string('note_warehouse', 500)->nullable()->comment('Ghi chú kho');
            $table->uuid('customer_delivery_id')->comment('Địa chỉ nhận hàng');
            $table->string('postcode', 15)->nullable()->comment('Mã bưu kiện');
            $table->string('shipper_phone_number', 15)->nullable()->comment('Số điện thoại');
            $table->boolean('is_received')->default(0)->comment('Đã nhận hàng');
            $table->string('status', 20)->default(DeliveryConstant::KEY_STATUS_PENDING)->comment(
                'Trạng thái'
            );
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
        Schema::dropIfExists('deliveries');
    }
}
