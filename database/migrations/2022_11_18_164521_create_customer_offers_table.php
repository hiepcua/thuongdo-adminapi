<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->string('customer_id');
            $table->integer('offer_rate')->default(0);
            $table->integer('deposit_rate')->default(0);
            $table->decimal('trans_weight_hn_fee')->default(0)->comment('Phí vận chuyển HN theo Khối lượng');
            $table->decimal('trans_weight_hcm_fee')->default(0)->comment('Phí vận chuyển HCM theo Khối lượng');
            $table->decimal('trans_weight_hp_fee')->default(0)->comment('Phí vận chuyển HP theo Khối lượng');
            $table->decimal('trans_volumn_hn_fee')->default(0)->comment('Phí vận chuyển HN theo dung lượng');
            $table->decimal('trans_volumn_hcm_fee')->default(0)->comment('Phí vận chuyển HCM theo dung lượng');
            $table->decimal('trans_volumn_hp_fee')->default(0)->comment('Phí vận chuyển HP theo dung lượng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_offers');
    }
}
