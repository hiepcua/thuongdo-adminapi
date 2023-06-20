<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('log_name');
            $table->nullableUuidMorphs('subject');
            $table->nullableUuidMorphs('causer');
            $table->uuid('object_id')->nullable()->comment('Dành cho đơn hàng: Giao hàng, Kiện hàng, đơn hàng, khiếu nại,...');
            $table->string('content');
            $table->json('properties')->nullable();
            $table->uuid('organization_id');
            $table->timestamps();
            $table->index('log_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
