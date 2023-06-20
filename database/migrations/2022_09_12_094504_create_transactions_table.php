<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('organization_id');
            $table->string('code')->nullable();
            $table->nullableUuidMorphs('sourceable');
            $table->decimal('amount', 20)->comment('Số tiền');
            $table->decimal('balance', 20)->comment('Số dư');
            $table->dateTime('time')->comment('Ngày thực hiện');
            $table->string('status', 20)->comment('Loại giao dịch');
            $table->string('content', 500)->nullable()->comment('Nội dung giao dịch');
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
        Schema::dropIfExists('transactions');
    }
}
