<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bank_id')->nullable()->comment('Ngân hang');
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('branch')->nullable();
            $table->uuid('supplier_id')->nullable()->comment('Nhà cung cấp.');
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
        Schema::dropIfExists('supplier_banks');
    }
}
