<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportDashboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_dashboards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('customers_numbers')->default(0)->comment('Số lượng khách hàng mới');
            $table->integer('customers_ordered_numbers')->default(0)->comment('Số lượng khách hàng mới đặt hàng');
            $table->integer('customers_has_some_orders_numbers')->default(0)->comment('Số lượng khách hàng đã đặt 2 -5 đơn');
            $table->integer('orders_numbers')->default(0)->comment('Số lượng đơn đặt hàng');
            $table->integer('orders_done_numbers')->default(0)->comment('Số lượng đơn hoàn thành');
            $table->integer('orders_complain_numbers')->default(0)->comment('Số lượng đơn khiếu lại');
            $table->date('report_at')->default(today());
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
        Schema::dropIfExists('report_dashboards');
    }
}
