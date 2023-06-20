<?php

use App\Constants\ConsignmentConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->comment('Mã đơn hàng');
            $table->uuid('warehouse_cn')->comment('Kho nhận hàng TQ');
            $table->uuid('warehouse_vi')->comment('Kho trả hàng VN');
            $table->uuid('customer_id')->comment('Khách hàng');
            $table->uuid('customer_delivery_id')->comment('Địa chỉ nhận hàng');
            $table->uuid('delivery_id')->comment('Giao hàng');
            $table->uuid('organization_id')->comment('Tổ chức');
            $table->integer('packages_number')->comment('Số Kiện');
            $table->integer('deliveries_number')->comment('Số Giao Hàng');
            $table->string('status', 20)->default(ConsignmentConstant::KEY_STATUS_PENDING)->comment(
                'Trạng thái đơn hàng'
            );
            $table->dateTime('date_ordered')->nullable();
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
        Schema::dropIfExists('consignments');
    }
}
