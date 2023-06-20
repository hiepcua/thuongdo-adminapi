<?php

use App\Constants\CustomerConstant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_withdrawal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id');
            $table->uuid('customer_id');
            $table->string('code');
            $table->string('account_holder');
            $table->string('account_number');
            $table->string('bank');
            $table->string('branch');
            $table->decimal('amount', 20)->default(0)->comment('Số tiền rút');
            $table->decimal('balance', 20)->default(0)->comment('Số dư');
            $table->string('status', 20)->default(CustomerConstant::KEY_WITHDRAWAL_STATUS_PENDING);
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
        Schema::dropIfExists('customer_withdrawal');
    }
}
