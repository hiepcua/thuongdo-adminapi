<?php

use App\Constants\OrderConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderStatusTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status_times', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            foreach (OrderConstant::STATUSES as $key => $value) {
                $table->dateTime($key)->nullable()->comment($value);
            }
            $table->timestamps();

            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status_times');
    }
}
