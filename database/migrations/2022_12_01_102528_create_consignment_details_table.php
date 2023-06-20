<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignment_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consignment_id');
            $table->uuid('order_package_id')->nullable();
            $table->integer('quantity')->default(0)->comment('Số lượng sản phẩm, Số lượng kiểm đếm');
            $table->decimal('order_cost', 20)->default(0)->comment('Giá trị hàng hóa');
            $table->string('name')->nullable()->comment('Tên sản phẩm');
            $table->uuid('category_id')->nullable()->comment('Ký gửi - Danh mục');
            $table->string('image')->nullable()->comment('Ký gửi - Hình ảnh');
            $table->string('packages_number')->nullable()->comment('Ký gửi - Số kiện');
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
        Schema::dropIfExists('consignment_details');
    }
}
