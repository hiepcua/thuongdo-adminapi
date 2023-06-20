<?php

use App\Constants\OrderConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->integer('orders_number')->comment('Số đơn hàng của khách')->default(0);
            $table->decimal('order_amount', 20)->comment('Số tiền khách hàng đã đặt hàng thành công')->default(0);
            $table->integer('consignment_number')->comment('Số đơn hàng ký gửi của khách')->default(0);
            $table->integer('packages_received_number')->comment('Số kiện hàng đã nhận của khách')->default(0);
            $table->integer('packages_number')->comment('Số kiện hàng của khách')->default(0);
            $table->decimal('deposited_amount', 20)->comment('Số tiền đã nạp')->default(0);
            $table->decimal('balance_amount', 20)->comment('Số dư')->default(0);
            $table->decimal('withdrawal_amount', 20)->comment('Số tiền rút')->default(0);
            $table->decimal('purchase_amount', 20)->comment('Số tiền thanh toán')->default(0);
            $table->decimal('discount_amount', 20)->comment('Số tiền hoàn')->default(0);
            foreach (OrderConstant::STATUSES as $key => $status) {
                $table->integer($key)->comment($status)->default(0);
            }
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
        Schema::dropIfExists('report_customers');
    }
}
