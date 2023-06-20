<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->uuid('id');
            $table->increments('id_number');
            $table->uuid('organization_id')->nullable();

            $table->string('name')->nullable();

            $table->tinyInteger('type_currency')->default(1); // Loại tiền tệ: Tiền mặt hay tiền ngân hàng
            $table->tinyInteger('unit_currency')->default(1); // Đơn vị tiền tệ: VND hay Yên

            $table->double('initial_balance')->unsigned()->default(0)->nullable(); // Số dư ban đầu
            $table->double('total_money_in')->unsigned()->default(0)->nullable(); // Tổng tiền vào
            $table->double('total_money_out')->unsigned()->default(0)->nullable(); // Tổn tiền ra
            $table->double('current_balance')->unsigned()->default(0)->nullable(); // Số dư hiện tại

            $table->string('note')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('funds');
    }
}
