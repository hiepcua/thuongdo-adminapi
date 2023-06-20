<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackageNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_package_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_package_id');
            $table->uuidMorphs('cause');
            $table->string('content', 500);
            $table->string('type', 15)->default('note')->comment('Loại ghi chú: NV Đặt hàng, Ghi Chú');
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
        Schema::dropIfExists('order_package_notes');
    }
}
