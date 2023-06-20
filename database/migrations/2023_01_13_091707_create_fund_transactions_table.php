<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fund_transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->increments('id_number');
            $table->uuid('organization_id')->nullable();

            $table->uuid('user_create_id')->nullable(); // Người tạo

            $table->string('code')->nullable(); // Mã giao dịch

            $table->tinyInteger('type_object')->default(1); // Đối tượng
            $table->tinyInteger('type_pay')->default(1); // Loại thanh toán: Thu hay chi
            $table->uuid('fund_type_pay_id')->nullable(); // Thanh toán thu chi cho cái gì
            $table->integer('fund_type_pay_code')->default(0)->unsigned()->nullable();

            $table->uuid('fund_id')->nullable(); // Giao dịch liên quan đến quỹ nào
            $table->string('fund_name')->nullable(); // Loại sổ là gì
            $table->tinyInteger('fund_type_currency')->default(1); // Loại sổ là gì
            $table->tinyInteger('fund_unit_currency')->default(1); // Tiền này là loại gì: Việt Nam hay TQ

            $table->string('customer_code')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            $table->double('money')->unsigned()->default(0)->nullable();
            $table->double('money_update')->unsigned()->default(0)->nullable();
            $table->double('balance')->unsigned()->default(0)->nullable(); // Số dư hiện tại

            $table->double('fee_customer')->unsigned()->default(0)->nullable(); // Phí khách hàng
            $table->double('fee_customer_ratio')->unsigned()->default(0)->nullable(); // Tỉ giá Phí khách hàng
            $table->double('fee_system')->unsigned()->default(0)->nullable(); // Phí hệ thống
            $table->double('fee_system_ratio')->unsigned()->default(0)->nullable(); // Tỉ giá Phí hệ thống


            $table->double('cn_change')->unsigned()->default(0)->nullable(); // Tổng tệ đổi
            $table->double('cn_change_ratio')->unsigned()->default(0)->nullable(); // Tỉ giá Tổng tệ đổi
            $table->double('fee_change_cn')->unsigned()->default(0)->nullable(); // Phí đổi tệ
            $table->double('fee_change_vnd')->unsigned()->default(0)->nullable(); // Phí đổi VND

            $table->uuid('fund_transaction_id')->nullable(); // Dùng để check 2 giao dịch ví nào có liên quan tới nhau
            $table->uuid('fund_transaction_update_id')->nullable(); // Dùng để check bản ghi này dc tạo ra do fund_transaction_id nó update mà ra
            $table->uuid('customer_withdrawal_id')->nullable(); // Giao dịch này liên quan đến rút tiền nào

            $table->string('note')->nullable();

            $table->text('logs')->nullable(); // Log lịch sử thay đổi

            $table->tinyInteger('status')->default(1); // Dùng để ẩn hiện bản ghi
            $table->tinyInteger('lock')->default(0); // Dùng để khóa ko cho thao tác gì nữa

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
        Schema::dropIfExists('fund_transactions');
    }
}
