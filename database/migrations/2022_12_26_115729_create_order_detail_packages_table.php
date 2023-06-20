<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'order_detail_packages',
            function (Blueprint $table) {
                $table->uuid('order_detail_id');
                $table->uuid('order_package_id');
                $table->integer('quantity')->default(0);
                $table->timestamps();
                $table->primary(['order_detail_id', 'order_package_id']);
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail_packages');
    }
}
