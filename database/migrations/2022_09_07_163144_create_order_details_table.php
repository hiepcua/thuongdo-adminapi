<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('order_id')->comment('Đơn hàng');
            $table->string('name')->comment('Tên sản phẩm');
            $table->string('link')->nullable()->comment('Link sản phẩm');
            $table->string('image')->nullable()->comment('Hình ảnh sản phẩm');
            $table->string('note')->nullable()->comment('Ghi chú');
            $table->string('classification')->nullable()->comment('Phân loại hàng');
            $table->decimal('unit_price_cny', 10)->default(0)->comment('Đơn giá TQ');
            $table->integer('quantity')->default(0)->comment('Số lượng');
            $table->integer('packages_number')->default(1)->comment('Số kiện');
            $table->integer('note_number')->default(0)->comment('Số ghi chú');
            $table->decimal('amount_cny', 10)->default(0)->comment('Thành Tiền');
            $table->uuid('supplier_id')->comment('Nhà cung cấp');
            $table->uuid('category_id')->nullable()->comment('Danh mục hàng hóa');
            $table->uuid('order_package_id')->nullable()->comment('Kiện hàng');
            $table->uuid('complain_id')->nullable()->comment('Khiếu nại');
            $table->string('complain_note')->nullable()->comment('Ghi chú khiếu nại');
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
        Schema::dropIfExists('order_details');
    }
}
