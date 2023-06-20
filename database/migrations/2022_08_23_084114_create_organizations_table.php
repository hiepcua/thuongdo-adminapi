<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'organizations',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code')->comment('Mã tổ chức');
                $table->string('name');
                $table->string('email');
                $table->string('phone_number');
                $table->string('avatar')->nullable();
                $table->string('address')->nullable();
                $table->string('tax_code')->nullable();
                $table->string('representative_name')->nullable()->comment('Người đại diện');
                $table->string('representative_phone')->nullable()->comment('Số điện thoại Người đại diện');
                $table->string('domain')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
