<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transporters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('short_name');
            $table->boolean('is_delivery_type')->default(1);
            $table->boolean('is_get_delivery_price')->default(0);
            $table->tinyInteger('order')->default(1);
            $table->boolean('has_children')->default(0);
            $table->string('country', 2)->default(\App\Constants\LocateConstant::COUNTRY_VI);
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
        Schema::dropIfExists('transporters');
    }
}
