<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTypePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_type_pays', function (Blueprint $table) {
            $table->uuid('id');
            $table->increments('id_number');
            $table->uuid('organization_id')->nullable();

            $table->string('name')->nullable();
            $table->tinyInteger('type')->default(1); // Loại thanh toán
            $table->integer('code')->default(0)->unsigned()->nullable(); // Mã các loại thanh toán mặc định do dev fix cứng

            $table->tinyInteger('status')->default(1); // Dùng để ẩn hiện bản ghi
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
        Schema::dropIfExists('fund_type_pays');
    }
}
