<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail_image', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_detail_id');
            $table->uuid('complain_id')->nullable();
            $table->string('image')->nullable()->comment('Ảnh khiếu nại sản phẩm');
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
        Schema::dropIfExists('order_detail_image');
    }
}
