<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code');
            $table->string('address')->nullable();
            $table->uuid('customer_id')->nullable();
            $table->uuid('organization_id');
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('order_amount')->nullable();
            $table->string('complain_number')->nullable();
            $table->enum('type', ['online', 'offline'])->default('online')->comment('Loại nhà cung cấp.');
            $table->uuid('industry')->nullable()->comment('Ngành hàng kinh doanh.');
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
        Schema::dropIfExists('suppliers');
    }
}
