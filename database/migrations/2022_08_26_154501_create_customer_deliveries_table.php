<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_deliveries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->string('receiver');
            $table->string('address');
            $table->string('phone_number', 13);
            $table->uuid('ward_id')->nullable();
            $table->uuid('district_id')->nullable();
            $table->uuid('province_id')->nullable();
            $table->decimal('delivery_cost', 20)->default(0)->comment('Phí vận chuyển tạm tính tại thời điểm tạo giao hàng');
            $table->tinyInteger('is_default')->default(0);
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
        Schema::dropIfExists('customer_deliveries');
    }
}
