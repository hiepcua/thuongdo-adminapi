<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cart_id');
            $table->string('name');
            $table->string('image');
            $table->string('link');
            $table->decimal('unit_price_cny', 10)->default(0)->comment('Đơn giá TQ');
            $table->integer('quantity')->default(0)->comment('Số lượng');
            $table->decimal('amount_cny', 20)->default(0)->comment('Thành tiền TQ');
            $table->string('classification', 500)->nullable()->comment('Phân loại hàng');
            $table->string('note', 500)->nullable()->comment('Ghi chú riêng');
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
        Schema::dropIfExists('cart_details');
    }
}
