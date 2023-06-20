<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplainImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complain_image', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('complain_id');
            $table->string('image')->comment('Ảnh thực tế nhận được hoặc ảnh của bill');
            $table->boolean('is_bill')->default(0);
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
        Schema::dropIfExists('complain_image');
    }
}
