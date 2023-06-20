<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'complain_details',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('complain_id');
                $table->uuid('order_detail_id');
                $table->uuid('order_package_id');
                $table->string('note', 500)->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('complain_details');
    }
}
